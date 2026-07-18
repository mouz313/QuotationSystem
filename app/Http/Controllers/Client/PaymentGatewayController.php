<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\PaymentSubmittedMail;
use App\Models\ClientUser;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentIntent;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentGatewayController extends Controller
{
    public function createStripeSession(Request $request, Quotation $quotation)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        if (!in_array($quotation->status, ['sent', 'opened', 'accepted'])) {
            return back()->with('error', 'Online payments can only be made on active quotations.');
        }

        $totalPaid = $quotation->payments()->where('status', 'approved')->sum('amount');
        $remaining = max(0, $quotation->grand_total - $totalPaid);

        if ($remaining <= 0) {
            return back()->with('error', 'This quotation is already fully paid.');
        }

        $stripeKey = config('services.stripe.secret');
        if (!$stripeKey) {
            return back()->with('error', 'Stripe is not configured. Please use manual payment.');
        }

        try {
            $response = Http::withBasicAuth($stripeKey, '')
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'payment_method_types[]' => 'card',
                    'line_items[0][price_data][currency]' => strtolower($quotation->currency?->code ?? 'usd'),
                    'line_items[0][price_data][unit_amount]' => (int) ($remaining * 100),
                    'line_items[0][price_data][product_data][name]' => "Quotation {$quotation->quote_number}",
                    'line_items[0][quantity]' => 1,
                    'mode' => 'payment',
                    'success_url' => route('client.quotations.payment-success', $quotation) . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('client.quotations.payment-cancel', $quotation),
                    'client_reference_id' => "Q-{$quotation->id}-" . time(),
                    'metadata[quotation_id]' => $quotation->id,
                    'metadata[client_user_id]' => $request->user('client')->id,
                ]);

            if ($response->successful()) {
                $session = $response->json();

                PaymentIntent::create([
                    'quotation_id'      => $quotation->id,
                    'client_user_id'    => $request->user('client')->id,
                    'amount'            => $remaining,
                    'currency_code'     => $quotation->currency?->code ?? 'USD',
                    'gateway'           => 'stripe',
                    'gateway_intent_id' => $session['id'],
                    'status'            => 'processing',
                ]);

                return redirect($session['url']);
            }

            Log::warning('Stripe session creation failed: ' . $response->body());
            return back()->with('error', 'Failed to create payment session. Please try again or use manual payment.');
        } catch (\Exception $e) {
            Log::warning('Stripe error: ' . $e->getMessage());
            return back()->with('error', 'Payment gateway error. Please try again or use manual payment.');
        }
    }

    public function createPayPalOrder(Request $request, Quotation $quotation)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        if (!in_array($quotation->status, ['sent', 'opened', 'accepted'])) {
            return back()->with('error', 'Online payments can only be made on active quotations.');
        }

        $totalPaid = $quotation->payments()->where('status', 'approved')->sum('amount');
        $remaining = max(0, $quotation->grand_total - $totalPaid);

        if ($remaining <= 0) {
            return back()->with('error', 'This quotation is already fully paid.');
        }

        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');
        $mode = config('services.paypal.mode', 'sandbox');

        if (!$clientId || !$clientSecret) {
            return back()->with('error', 'PayPal is not configured. Please use manual payment.');
        }

        try {
            $baseUrl = $mode === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $tokenResponse = Http::asForm()->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ])->withBasicAuth($clientId, $clientSecret);

            if (!$tokenResponse->successful()) {
                return back()->with('error', 'PayPal authentication failed. Please try again.');
            }

            $accessToken = $tokenResponse->json('access_token');

            $orderResponse = Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => "Q-{$quotation->id}-" . time(),
                        'amount' => [
                            'currency_code' => $quotation->currency?->code ?? 'USD',
                            'value' => number_format($remaining, 2, '.', ''),
                        ],
                        'description' => "Payment for Quotation {$quotation->quote_number}",
                    ]],
                    'application_context' => [
                        'return_url' => route('client.quotations.payment-success', $quotation),
                        'cancel_url' => route('client.quotations.payment-cancel', $quotation),
                    ],
                ]);

            if ($orderResponse->successful()) {
                $order = $orderResponse->json();
                $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                if ($approveUrl) {
                    PaymentIntent::create([
                        'quotation_id'      => $quotation->id,
                        'client_user_id'    => $request->user('client')->id,
                        'amount'            => $remaining,
                        'currency_code'     => $quotation->currency?->code ?? 'USD',
                        'gateway'           => 'paypal',
                        'gateway_intent_id' => $order['id'],
                        'status'            => 'processing',
                    ]);

                    return redirect($approveUrl);
                }
            }

            Log::warning('PayPal order creation failed: ' . $orderResponse->body());
            return back()->with('error', 'Failed to create PayPal order. Please try again or use manual payment.');
        } catch (\Exception $e) {
            Log::warning('PayPal error: ' . $e->getMessage());
            return back()->with('error', 'Payment gateway error. Please try again or use manual payment.');
        }
    }

    public function handleSuccess(Request $request, Quotation $quotation)
    {
        $sessionId = $request->query('session_id');
        $payerId = $request->query('PayerID');
        $token = $request->query('token');

        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        $clientUser = $request->user('client');

        if ($sessionId) {
            $intent = PaymentIntent::where('gateway_intent_id', $sessionId)
                ->where('quotation_id', $quotation->id)
                ->where('status', 'processing')
                ->first();

            if ($intent) {
                $this->captureStripePayment($quotation, $intent, $clientUser);
            }
        } elseif ($token) {
            $intent = PaymentIntent::where('gateway', 'paypal')
                ->where('quotation_id', $quotation->id)
                ->where('status', 'processing')
                ->latest()
                ->first();

            if ($intent) {
                $this->capturePayPalPayment($quotation, $intent, $clientUser, $token);
            }
        }

        return redirect("/client/quotations/{$quotation->id}")
            ->with('success', 'Online payment processed successfully!');
    }

    public function handleCancel(Request $request, Quotation $quotation)
    {
        return redirect("/client/quotations/{$quotation->id}")
            ->with('error', 'Payment was cancelled.');
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if ($webhookSecret && $sigHeader) {
            try {
                $elements = [];
                $parts = explode(',', $sigHeader);
                foreach ($parts as $part) {
                    [$key, $value] = explode('=', $part, 2);
                    $elements[$key] = $value;
                }

                $timestamp = $elements['t'] ?? '';
                $signature = $elements['v1'] ?? '';
                $signedPayload = $timestamp . '.' . $payload;
                $expectedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);

                if (!hash_equals($expectedSignature, $signature)) {
                    return response('Invalid signature', 400);
                }
            } catch (\Exception $e) {
                return response('Signature verification failed', 400);
            }
        }

        $event = json_decode($payload, true);

        if ($event['type'] === 'checkout.session.completed') {
            $sessionData = $event['data']['object'];
            $this->processStripeWebhook($sessionData);
        }

        return response('OK');
    }

    private function captureStripePayment(Quotation $quotation, PaymentIntent $intent, ClientUser $clientUser): void
    {
        $stripeKey = config('services.stripe.secret');

        try {
            $response = Http::withBasicAuth($stripeKey, '')
                ->get("https://api.stripe.com/v1/checkout/sessions/{$intent->gateway_intent_id}");

            if ($response->successful() && $response->json('payment_status') === 'paid') {
                $payment = Payment::create([
                    'quotation_id'      => $quotation->id,
                    'client_user_id'    => $clientUser->id,
                    'amount'            => $intent->amount,
                    'payment_method'    => 'online',
                    'transaction_id'    => $intent->gateway_intent_id,
                    'paid_via'          => 'stripe',
                    'gateway_response'  => $response->json(),
                    'status'            => 'approved',
                    'reviewed_at'       => now(),
                ]);

                $intent->update(['status' => 'completed', 'paid_at' => now()]);

                $this->updateQuotationPaymentStatus($quotation);
                $this->sendPaymentEmails($quotation, $payment, $clientUser);
            }
        } catch (\Exception $e) {
            Log::warning('Stripe capture error: ' . $e->getMessage());
        }
    }

    private function capturePayPalPayment(Quotation $quotation, PaymentIntent $intent, ClientUser $clientUser, string $token): void
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');
        $mode = config('services.paypal.mode', 'sandbox');

        try {
            $baseUrl = $mode === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $tokenResponse = Http::asForm()->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ])->withBasicAuth($clientId, $clientSecret);

            $accessToken = $tokenResponse->json('access_token');

            $captureResponse = Http::withToken($accessToken)
                ->post("{$baseUrl}/v2/checkout/orders/{$intent->gateway_intent_id}/capture");

            if ($captureResponse->successful()) {
                $captureData = $captureResponse->json();
                $captureId = $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

                $payment = Payment::create([
                    'quotation_id'      => $quotation->id,
                    'client_user_id'    => $clientUser->id,
                    'amount'            => $intent->amount,
                    'payment_method'    => 'online',
                    'transaction_id'    => $captureId ?? $intent->gateway_intent_id,
                    'paid_via'          => 'paypal',
                    'gateway_response'  => $captureData,
                    'status'            => 'approved',
                    'reviewed_at'       => now(),
                ]);

                $intent->update(['status' => 'completed', 'paid_at' => now()]);

                $this->updateQuotationPaymentStatus($quotation);
                $this->sendPaymentEmails($quotation, $payment, $clientUser);
            }
        } catch (\Exception $e) {
            Log::warning('PayPal capture error: ' . $e->getMessage());
        }
    }

    private function processStripeWebhook(array $sessionData): void
    {
        $intent = PaymentIntent::where('gateway_intent_id', $sessionData['id'])
            ->where('status', 'processing')
            ->with(['quotation', 'clientUser'])
            ->first();

        if (!$intent) return;

        if ($sessionData['payment_status'] === 'paid') {
            $quotation = $intent->quotation;
            $clientUser = $intent->clientUser;

            $payment = Payment::create([
                'quotation_id'      => $quotation->id,
                'quotation_item_id' => $intent->quotation_item_id,
                'client_user_id'    => $intent->client_user_id,
                'amount'            => $intent->amount,
                'payment_method'    => 'online',
                'transaction_id'    => $intent->gateway_intent_id,
                'paid_via'          => 'stripe',
                'gateway_response'  => $sessionData,
                'status'            => 'approved',
                'reviewed_at'       => now(),
            ]);

            $intent->update(['status' => 'completed', 'paid_at' => now()]);

            $this->updateQuotationPaymentStatus($quotation);
            $this->sendPaymentEmails($quotation, $payment, $clientUser);
        }
    }

    private function updateQuotationPaymentStatus(Quotation $quotation): void
    {
        $totalPaid = $quotation->payments()->where('status', 'approved')->sum('amount');

        if ($quotation->isMilestone()) {
            if (!$quotation->relationLoaded('items')) {
                $quotation->load('items.payments');
            }
            $allMilestonesPaid = true;
            foreach ($quotation->items as $item) {
                $itemPaid = $item->payments->where('status', 'approved')->sum('amount');
                if ($itemPaid < $item->subtotal) {
                    $allMilestonesPaid = false;
                    break;
                }
            }
            $paymentStatus = $allMilestonesPaid ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
        } else {
            $paymentStatus = $totalPaid >= $quotation->grand_total ? 'paid' : 'partial';
        }

        $quotation->update([
            'payment_status' => $paymentStatus,
            'paid_amount'    => $totalPaid,
            'paid_at'        => $paymentStatus === 'paid' ? now() : null,
        ]);
    }

    private function sendPaymentEmails(Quotation $quotation, Payment $payment, ClientUser $clientUser): void
    {
        if (!$quotation->relationLoaded('user')) {
            $quotation->load('user');
        }

        try {
            Mail::to($quotation->user->email)->send(
                new PaymentSubmittedMail($quotation, $payment, $clientUser)
            );
        } catch (\Exception $e) {
            Log::warning('Email failed (online payment notification): ' . $e->getMessage());
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type'    => 'payment_submitted',
            'message' => "{$clientUser->name} submitted an online payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " for {$quotation->quote_number}.",
            'url'     => "/quotations/{$quotation->id}",
        ]);
    }

    private function userCanAccess(Request $request, Quotation $quotation): bool
    {
        if (!$quotation->relationLoaded('user')) {
            $quotation->load('user');
        }
        $companyIds = $request->user('client')->companies()->pluck('companies.id');
        return $companyIds->contains($quotation->user->company_id);
    }
}
