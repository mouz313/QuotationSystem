@extends('layouts.admin')
@section('title', 'Package Orders')
@section('header-title', 'Package Orders')
@section('header-sub', 'Manage self-service package purchase orders')
@section('content')

<div class="d-card fade-in">
    <div class="d-card-header">
        <h3>All Orders</h3>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.package-orders.index') }}" class="filter-pill {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.package-orders.index', ['status' => 'pending']) }}" class="filter-pill {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('admin.package-orders.index', ['status' => 'paid']) }}" class="filter-pill {{ request('status') === 'paid' ? 'active' : '' }}">Paid</a>
            <a href="{{ route('admin.package-orders.index', ['status' => 'failed']) }}" class="filter-pill {{ request('status') === 'failed' ? 'active' : '' }}">Failed</a>
        </div>
    </div>

    @if($orders->count() > 0)
    <div class="overflow-x-auto">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Company</th>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="cell-main">#{{ $order->id }}</td>
                    <td>{{ $order->company->name ?? 'N/A' }}</td>
                    <td>{{ $order->package->name ?? 'N/A' }}</td>
                    <td class="cell-main">{{ $order->currency_symbol }}{{ number_format($order->amount, 2) }}</td>
                    <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="text-gray-400 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        @if($order->status === 'pending')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.package-orders.approve', $order) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:var(--emerald-500);color:white;">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.package-orders.reject', $order) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        </div>
                        @else
                        <span class="text-gray-400 text-xs">&mdash;</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div style="padding:.75rem 1rem;border-top:1px solid var(--gray-100);">
        {{ $orders->links() }}
    </div>
    @endif
    @else
    <div class="empty-state">
        <div class="empty-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <h3>No package orders yet</h3>
        <p>Orders will appear here when companies purchase packages through the self-service flow.</p>
    </div>
    @endif
</div>
@endsection
