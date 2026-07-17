@php $company = $quotation->user->company; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quotation->quote_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --brand-500: oklch(0.55 0.17 275);
            --brand-600: oklch(0.48 0.19 275);
        }
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
    </style>
</head>
<body style="background:var(--surface-50,#f8fafc);padding:2rem;">
    <div style="max-width:56rem;margin:0 auto;background:white;border-radius:.75rem;box-shadow:0 12px 40px oklch(0 0 0 / .08);padding:2rem;" class="print:shadow-none print:p-0">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid var(--brand-600);padding-bottom:1.5rem;margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                @if($company && $company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="Logo" style="width:4rem;height:4rem;border-radius:.5rem;object-fit:cover;">
                @endif
                <div>
                    @if($company)<div style="font-size:1.5rem;font-weight:800;color:var(--brand-600);">{{ $company->name }}</div>@endif
                    @if($company && $company->email)<div style="font-size:.8125rem;color:var(--surface-500);">{{ $company->email }}</div>@endif
                    @if($company && $company->phone)<div style="font-size:.8125rem;color:var(--surface-500);">{{ $company->phone }}</div>@endif
                    @if($company && $company->address)<div style="font-size:.8125rem;color:var(--surface-500);">{{ $company->address }}</div>@endif
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:1.875rem;font-weight:800;color:var(--brand-600);text-transform:uppercase;">Quotation</div>
                <div style="font-size:1.125rem;font-weight:700;color:var(--surface-700);margin-top:.25rem;">{{ $quotation->quote_number }}</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
            <div>
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);">Issue Date</div>
                <div style="font-weight:700;color:var(--surface-800);">{{ $quotation->issue_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);">Expiry Date</div>
                <div style="font-weight:700;color:var(--surface-800);">{{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);">Status</div>
                <div style="font-weight:700;text-transform:capitalize;color:var(--surface-800);">{{ $quotation->status }}</div>
            </div>
        </div>

        <div style="margin-bottom:2rem;">
            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);margin-bottom:.25rem;">Bill To</div>
            <div style="font-weight:700;font-size:1.125rem;color:var(--surface-900);">{{ $quotation->client->name }}</div>
            <div style="color:var(--surface-600);font-size:.875rem;">{{ $quotation->client->email }}</div>
            @if($quotation->client->phone)<div style="color:var(--surface-600);font-size:.875rem;">{{ $quotation->client->phone }}</div>@endif
            @if($quotation->client->address)<div style="color:var(--surface-600);font-size:.875rem;">{{ $quotation->client->address }}</div>@endif
        </div>

        <table style="width:100%;margin-bottom:2rem;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--brand-600);color:white;font-size:.8125rem;text-transform:uppercase;">
                    <th style="padding:.75rem;text-align:left;">#</th>
                    <th style="padding:.75rem;text-align:left;">Item</th>
                    <th style="padding:.75rem;text-align:left;">Description</th>
                    <th style="padding:.75rem;text-align:right;">Qty</th>
                    <th style="padding:.75rem;text-align:right;">Unit Price</th>
                    <th style="padding:.75rem;text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr style="border-bottom:1px solid var(--surface-200);">
                    <td style="padding:.75rem;color:var(--surface-600);">{{ $index + 1 }}</td>
                    <td style="padding:.75rem;font-weight:600;color:var(--surface-800);">{{ $item->item_title }}</td>
                    <td style="padding:.75rem;color:var(--surface-600);">{{ $item->item_description ?? '-' }}</td>
                    <td style="padding:.75rem;text-align:right;color:var(--surface-700);">{{ $item->quantity }}</td>
                    <td style="padding:.75rem;text-align:right;color:var(--surface-700);">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding:.75rem;text-align:right;font-weight:600;color:var(--surface-800);">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display:flex;justify-content:flex-end;">
            <div style="width:18rem;display:flex;flex-direction:column;gap:.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:.8125rem;"><span style="color:var(--surface-500);">Subtotal:</span><span style="font-weight:600;color:var(--surface-700);">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                @if($quotation->discount_amount > 0)
                <div style="display:flex;justify-content:space-between;font-size:.8125rem;"><span style="color:var(--surface-500);">Discount:</span><span style="font-weight:600;color:var(--danger-600);">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                @endif
                @if($quotation->tax_percentage > 0)
                <div style="display:flex;justify-content:space-between;font-size:.8125rem;"><span style="color:var(--surface-500);">Tax ({{ $quotation->tax_percentage }}%):</span><span style="font-weight:600;color:var(--surface-700);">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>
                @endif
                <div style="display:flex;justify-content:space-between;border-top:3px solid var(--brand-600);padding-top:.5rem;font-size:1.125rem;font-weight:800;color:var(--brand-600);"><span>Grand Total:</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
            </div>
        </div>

        @if($quotation->terms_conditions)
        <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--surface-200);">
            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--surface-400);margin-bottom:.5rem;">Terms & Conditions</div>
            <p style="font-size:.8125rem;color:var(--surface-600);line-height:1.6;">{{ $quotation->terms_conditions }}</p>
        </div>
        @endif

        <div style="margin-top:2rem;text-align:center;font-size:.7rem;color:var(--surface-400);border-top:1px solid var(--surface-200);padding-top:1rem;">Generated on {{ now()->format('M d, Y \a\t h:i A') }}</div>
    </div>
    <div style="text-align:center;margin-top:1rem;" class="print:hidden">
        <button onclick="window.print()" style="padding:.5rem 1.5rem;background:var(--brand-600);color:white;border-radius:.5rem;border:none;cursor:pointer;font-weight:600;font-size:.875rem;">Print</button>
    </div>
</body>
</html>
