<?php

namespace Tests\Feature\Payments;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Currency;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $package = Package::factory()->create([
            'max_quotations' => 1000,
            'max_clients'     => 1000,
            'max_users'       => 100,
            'duration_days'   => 365,
        ]);
        CompanyPackage::factory()->create([
            'company_id'  => $this->company->id,
            'package_id'  => $package->id,
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addYear()->toDateString(),
            'status'      => 'active',
        ]);
        $this->user = User::factory()->companyAdmin()->create([
            'company_id' => $this->company->id,
        ]);
        $this->currency = Currency::factory()->default()->create();
    }

    public function test_company_user_can_approve_payment(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $this->currency->id,
            'grand_total' => 1000,
            'status'      => 'sent',
        ]);
        $payment = Payment::factory()->create([
            'quotation_id' => $quotation->id,
            'amount'       => 500,
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post("/quotations/{$quotation->id}/payments/{$payment->id}/approve");
        $response->assertRedirect();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'approved']);
        $quotation->refresh();
        $this->assertEquals('partial', $quotation->payment_status);
    }

    public function test_company_user_can_reject_payment(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $this->currency->id,
            'grand_total' => 1000,
            'status'      => 'sent',
        ]);
        $payment = Payment::factory()->create([
            'quotation_id' => $quotation->id,
            'amount'       => 500,
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post("/quotations/{$quotation->id}/payments/{$payment->id}/reject", [
            'rejection_reason' => 'Invalid proof',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'rejected']);
    }

    public function test_full_payment_marks_quotation_as_paid(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $this->currency->id,
            'grand_total' => 1000,
            'status'      => 'sent',
        ]);
        $payment = Payment::factory()->create([
            'quotation_id' => $quotation->id,
            'amount'       => 1000,
            'status'       => 'pending',
        ]);

        $this->actingAs($this->user)->post("/quotations/{$quotation->id}/payments/{$payment->id}/approve");

        $quotation->refresh();
        $this->assertEquals('paid', $quotation->payment_status);
        $this->assertEquals(1000, $quotation->paid_amount);
        $this->assertNotNull($quotation->paid_at);
    }

    public function test_user_cannot_approve_other_users_payment(): void
    {
        $otherUser = User::factory()->create(['company_id' => $this->company->id]);
        $client = Client::factory()->create(['user_id' => $otherUser->id]);
        $quotation = Quotation::factory()->create([
            'user_id'     => $otherUser->id,
            'client_id'   => $client->id,
            'currency_id' => $this->currency->id,
        ]);
        $payment = Payment::factory()->create([
            'quotation_id' => $quotation->id,
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post("/quotations/{$quotation->id}/payments/{$payment->id}/approve");
        $response->assertStatus(403);
    }

    public function test_bulk_approve_payments(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $this->currency->id,
            'grand_total' => 5000,
            'status'      => 'sent',
        ]);
        $p1 = Payment::factory()->create(['quotation_id' => $quotation->id, 'amount' => 2000, 'status' => 'pending']);
        $p2 = Payment::factory()->create(['quotation_id' => $quotation->id, 'amount' => 1000, 'status' => 'pending']);

        $response = $this->actingAs($this->user)->post("/quotations/{$quotation->id}/payments/bulk-approve", [
            'payment_ids' => [$p1->id, $p2->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', ['id' => $p1->id, 'status' => 'approved']);
        $this->assertDatabaseHas('payments', ['id' => $p2->id, 'status' => 'approved']);
    }

    public function test_payment_cannot_exceed_grand_total(): void
    {
        $client = Client::factory()->create(['user_id' => $this->user->id]);
        $quotation = Quotation::factory()->create([
            'user_id'     => $this->user->id,
            'client_id'   => $client->id,
            'currency_id' => $this->currency->id,
            'grand_total' => 1000,
            'type'        => 'simple',
            'status'      => 'sent',
        ]);

        $clientUser = ClientUser::factory()->create();
        $clientUser->companies()->attach($this->company->id);
        $client->update(['client_user_id' => $clientUser->id]);

        $response = $this->actingAs($clientUser, 'client')->post("/client/quotations/{$quotation->id}/submit-payment", [
            'amount'         => 2000,
            'payment_method' => 'bank_transfer',
        ], ['accept' => 'text/html']);

        $response->assertSessionHas('error');
    }
}
