<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PackageOrderApprovedMail;
use App\Models\ActivityLog;
use App\Models\CompanyPackage;
use App\Models\Notification;
use App\Models\PackageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PackageOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PackageOrder::with(['company', 'package']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(setting_int('pagination_per_page', 15))->withQueryString();
        return view('admin.package-orders.index', compact('orders'));
    }

    public function approve(PackageOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be approved.');
        }

        $order->update([
            'status'     => 'paid',
            'notes'      => ($order->notes ?? '') . "\n\nApproved by admin",
        ]);

        CompanyPackage::where('company_id', $order->company_id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        CompanyPackage::create([
            'company_id'  => $order->company_id,
            'package_id'  => $order->package_id,
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addDays($order->package->duration_days)->toDateString(),
            'status'      => 'active',
        ]);

        $order->company->update(['status' => 'active']);

        ActivityLog::log('package_order_approved', $order, "Package order #{$order->id} approved");

        $companyAdmin = $order->company->users()->where('role', 'company_admin')->first();
        if ($companyAdmin) {
            Notification::create([
                'user_id' => $companyAdmin->id,
                'type'    => 'package_order_approved',
                'message' => "Your package order #{$order->id} for '{$order->package->name}' has been approved.",
                'url'     => '/company/settings',
            ]);

            try {
                Mail::to($companyAdmin->email)->send(
                    new PackageOrderApprovedMail($order, $order->package)
                );
            } catch (\Exception $e) {
                \Log::warning('Package order approved email failed: ' . $e->getMessage());
            }
        }

        event(new \App\Events\PackageAssigned($order->company, $order->package));

        return back()->with('success', "Order #{$order->id} approved. Package '{$order->package->name}' assigned to '{$order->company->name}'.");
    }

    public function reject(Request $request, PackageOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be rejected.');
        }

        $request->validate(['reason' => 'nullable|string|max:500']);

        $order->update([
            'status' => 'failed',
            'notes'  => ($order->notes ?? '') . "\n\nRejected by admin: " . ($request->reason ?? 'No reason given'),
        ]);

        ActivityLog::log('package_order_rejected', $order, "Package order #{$order->id} rejected");

        $companyAdmin = $order->company->users()->where('role', 'company_admin')->first();
        if ($companyAdmin) {
            Notification::create([
                'user_id' => $companyAdmin->id,
                'type'    => 'package_order_rejected',
                'message' => "Your package order #{$order->id} for '{$order->package->name}' has been rejected.",
                'url'     => '/company/settings',
            ]);
        }

        return back()->with('success', "Order #{$order->id} rejected.");
    }
}
