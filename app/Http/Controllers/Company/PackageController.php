<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\PackageOrderApprovedMail;
use App\Mail\PackageOrderConfirmationMail;
use App\Models\ActivityLog;
use App\Models\CompanyPackage;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PackageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PackageController extends Controller
{
    public function browse()
    {
        $packages = Package::where('is_active', true)->orderBy('price')->get();
        $currentPackage = auth()->user()->company?->activePackage();
        return view('company.packages.browse', compact('packages', 'currentPackage'));
    }

    public function show(Package $package)
    {
        $currentPackage = auth()->user()->company?->activePackage();
        return view('company.packages.show', compact('package', 'currentPackage'));
    }

    public function purchase(Request $request, Package $package)
    {
        $company = $request->user()->company;
        if (!$company) {
            return back()->with('error', 'No company associated with your account.');
        }

        if ($package->price == 0) {
            return back()->with('error', 'This is a free package and is already assigned during registration.');
        }

        $order = PackageOrder::create([
            'company_id'    => $company->id,
            'package_id'    => $package->id,
            'amount'        => $package->price,
            'currency_code' => $package->currency_code,
            'status'        => 'pending',
            'payment_method' => $request->payment_method ?? null,
        ]);

        ActivityLog::log('package_order_created', $order, "Package order #{$order->id} created for '{$package->name}'");

        try {
            Mail::to($request->user()->email)->send(
                new PackageOrderConfirmationMail($order, $package, $company)
            );
        } catch (\Exception $e) {
            \Log::warning('Package order confirmation email failed: ' . $e->getMessage());
        }

        $adminUsers = \App\Models\User::where('role', 'super_admin')->get();
        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'package_order_created',
                'message' => "{$company->name} has requested package '{$package->name}' ({$package->currency_symbol}{{ number_format($package->price, 2) }}).",
                'url'     => '/admin/package-orders',
            ]);
        }

        return redirect('/company/settings')->with('success', "Package order #{$order->id} has been submitted. You will be notified once it is approved.");
    }

    public function subscription()
    {
        $company = auth()->user()->company;
        $currentPackage = $company?->activePackage();
        $recentOrders = PackageOrder::where('company_id', $company?->id)
            ->with('package')
            ->latest()
            ->limit(setting_int('dashboard_limit', 10))
            ->get();

        return view('company.packages.subscription', compact('currentPackage', 'recentOrders'));
    }

    public function orderHistory()
    {
        $company = auth()->user()->company;
        $orders = PackageOrder::where('company_id', $company?->id)
            ->with('package')
            ->latest()
            ->paginate(setting_int('pagination_per_page', 15));

        return view('company.packages.orders', compact('orders'));
    }

    public function handlePurchaseSuccess(Request $request)
    {
        return redirect('/company/settings')->with('success', 'Payment processed successfully. Your package will be activated shortly.');
    }

    public function handlePurchaseCancel(Request $request)
    {
        return redirect('/packages')->with('error', 'Payment was cancelled.');
    }
}
