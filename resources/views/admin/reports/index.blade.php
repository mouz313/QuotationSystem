@extends('layouts.admin')
@section('title', 'Reports & Exports')
@section('content')
<div class="fade-in">
    <x-page-header title="Reports & Exports" subtitle="Generate and download reports in PDF or Excel format" />

    <div class="stat-grid" style="margin-bottom:2rem;">
        <div class="stat-card">
            <div class="stat-icon brand">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_companies']) }}</div>
                <div class="stat-label">Total Companies</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_quotations']) }}</div>
                <div class="stat-label">Total Quotations</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_revenue'], 2) }}</div>
                <div class="stat-label">Total Revenue (all currencies)</div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        {{-- Companies Report --}}
        <div class="d-card">
            <div class="d-card-header">
                <div>
                    <h3>Companies Report</h3>
                    <p style="font-size:.7rem;color:var(--surface-400);margin-top:.125rem;">All companies with packages and status</p>
                </div>
                <div style="display:flex;gap:.5rem;">
                    <a href="/admin/reports/companies/export?format=pdf" class="btn btn-sm" style="background:var(--danger-50);color:var(--danger-600);">PDF</a>
                    <a href="/admin/reports/companies/export?format=xlsx" class="btn btn-sm" style="background:var(--success-50);color:var(--success-600);">CSV</a>
                </div>
            </div>
        </div>

        {{-- Quotations Report --}}
        <div class="d-card">
            <div class="d-card-header">
                <div>
                    <h3>Quotations Report</h3>
                    <p style="font-size:.7rem;color:var(--surface-400);margin-top:.125rem;">Filter by status and date range</p>
                </div>
            </div>
            <div class="d-card-body">
                <form method="GET" action="/admin/reports/quotations/export" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:1;min-width:120px;">
                        <x-form-select name="status" label="Status" placeholder="All"
                            :options="['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'declined' => 'Declined']" />
                    </div>
                    <div style="min-width:150px;">
                        <x-form-input label="From" name="from_date" type="date" />
                    </div>
                    <div style="min-width:150px;">
                        <x-form-input label="To" name="to_date" type="date" />
                    </div>
                    <button type="submit" name="format" value="pdf" class="btn btn-sm" style="background:var(--danger-50);color:var(--danger-600);">PDF</button>
                    <button type="submit" name="format" value="xlsx" class="btn btn-sm" style="background:var(--success-50);color:var(--success-600);">CSV</button>
                </form>
            </div>
        </div>

        {{-- Revenue Report --}}
        <div class="d-card">
            <div class="d-card-header">
                <div>
                    <h3>Revenue Report</h3>
                    <p style="font-size:.7rem;color:var(--surface-400);margin-top:.125rem;">Monthly revenue from accepted quotations</p>
                </div>
            </div>
            <div class="d-card-body">
                <form method="GET" action="/admin/reports/revenue/export" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                    <div style="min-width:150px;">
                        <x-form-input label="From" name="from_date" type="date" />
                    </div>
                    <div style="min-width:150px;">
                        <x-form-input label="To" name="to_date" type="date" />
                    </div>
                    <button type="submit" name="format" value="pdf" class="btn btn-sm" style="background:var(--danger-50);color:var(--danger-600);">PDF</button>
                    <button type="submit" name="format" value="xlsx" class="btn btn-sm" style="background:var(--success-50);color:var(--success-600);">CSV</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
