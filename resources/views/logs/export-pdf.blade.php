<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Helvetica, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        .hero { background: #1d4ed8; color: #fff; padding: 28px 32px; }
        .hero-inner { display: table; width: 100%; }
        .hero-left  { display: table-cell; vertical-align: top; }
        .hero-right { display: table-cell; vertical-align: top; text-align: right; }
        .brand { font-size: 9px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #93c5fd; margin-bottom: 6px; }
        .employee-name { font-size: 20px; font-weight: 700; color: #fff; }
        .employee-sub  { font-size: 10px; color: #bfdbfe; margin-top: 3px; }
        .amount-label  { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #93c5fd; margin-bottom: 4px; }
        .amount-value  { font-size: 26px; font-weight: 700; color: #fff; }
        .period-text   { font-size: 10px; color: #bfdbfe; margin-top: 4px; }

        .meta-bar  { display: table; width: 100%; border-bottom: 1px solid #e5e7eb; }
        .meta-cell { display: table-cell; padding: 10px 20px; border-right: 1px solid #e5e7eb; width: 33.33%; }
        .meta-cell:last-child { border-right: none; }
        .meta-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 3px; }
        .meta-value { font-size: 11px; font-weight: 600; color: #374151; }

        .section-pad { padding: 0 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        thead tr { border-bottom: 2px solid #e5e7eb; }
        thead th { padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #6b7280; }
        thead th.r { text-align: right; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody td { padding: 7px 8px; font-size: 10.5px; color: #374151; }
        tbody td.r { text-align: right; }
        tbody td.amount { text-align: right; font-weight: 600; color: #111827; }
        tbody td.muted { color: #9ca3af; }

        .totals-wrap  { padding: 14px 20px; border-top: 1px solid #e5e7eb; background: #f9fafb; }
        .totals-table { width: 200px; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 3px 0; font-size: 10.5px; color: #6b7280; }
        .totals-table td.v { text-align: right; font-weight: 600; color: #374151; }
        .totals-table tr.grand td { padding-top: 8px; border-top: 2px solid #e5e7eb; font-size: 13px; font-weight: 700; color: #1d4ed8; }

        .pay-footer { padding: 12px 20px; border-top: 2px solid #dbeafe; background: #eff6ff; }
        .pay-footer.wise { border-top-color: #d1fae5; background: #f0fdf4; }
        .pay-label { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #1d4ed8; margin-bottom: 4px; }
        .pay-label.wise { color: #059669; }
        .pay-row  { display: table; width: 100%; }
        .pay-info { display: table-cell; vertical-align: middle; }
        .pay-cta  { display: table-cell; vertical-align: middle; text-align: right; }
        .pay-info p { font-size: 10.5px; color: #374151; }
        .pay-btn  { display: inline-block; padding: 6px 16px; border-radius: 6px; font-size: 10px; font-weight: 700; color: #fff; background: #0070ba; text-decoration: none; }
        .pay-btn.wise { background: #00b9a0; }

        .doc-footer       { padding: 8px 20px; border-top: 1px solid #f3f4f6; display: table; width: 100%; }
        .doc-footer-left  { display: table-cell; font-size: 8.5px; color: #9ca3af; }
        .doc-footer-right { display: table-cell; text-align: right; font-size: 8.5px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="hero">
    <div class="hero-inner">
        <div class="hero-left">
            <div class="brand">goTrackr &middot; Invoice</div>
            <div class="employee-name">{{ $employee->name }}</div>
            <div class="employee-sub">{{ $employee->email }}</div>
            @if($employee->company)
                <div class="employee-sub" style="margin-top:2px;">{{ $employee->company->name }}</div>
            @endif
        </div>
        <div class="hero-right">
            <div class="amount-label">Amount Due</div>
            <div class="amount-value">${{ number_format($totalPay, 2) }}</div>
            <div class="period-text">{{ $label }}</div>
        </div>
    </div>
</div>

<div class="meta-bar">
    <div class="meta-cell">
        <div class="meta-label">Invoice Type</div>
        <div class="meta-value" style="text-transform:capitalize;">{{ $type }}</div>
    </div>
    <div class="meta-cell">
        <div class="meta-label">Billing Period</div>
        <div class="meta-value">{{ $label }}</div>
    </div>
    <div class="meta-cell">
        <div class="meta-label">Hourly Rate</div>
        <div class="meta-value">${{ number_format($employee->hourly_rate, 2) }}/hr</div>
    </div>
</div>

<div class="section-pad">
    <table>
        <thead>
            <tr>
                <th style="width:85px;">Date</th>
                <th>Description</th>
                <th style="width:65px;">Clock In</th>
                <th style="width:65px;">Clock Out</th>
                <th class="r" style="width:50px;">Hours</th>
                <th class="r" style="width:65px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            @php $amt = $log->total_hours ? $log->total_hours * $employee->hourly_rate : 0; @endphp
            <tr>
                <td>{{ $log->date->format('M d, Y') }}</td>
                <td class="muted">{{ $log->description ?: '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                <td>{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : '—' }}</td>
                <td class="r muted">{{ $log->total_hours ?? '—' }}</td>
                <td class="amount">${{ number_format($amt, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#9ca3af;">No time logs for this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="totals-wrap">
    <table class="totals-table">
        <tr>
            <td>Total Hours</td>
            <td class="v">{{ number_format($totalHours, 2) }} hrs</td>
        </tr>
        <tr>
            <td>Rate</td>
            <td class="v">${{ number_format($employee->hourly_rate, 2) }}/hr</td>
        </tr>
        <tr class="grand">
            <td>Total Due</td>
            <td class="v">${{ number_format($totalPay, 2) }}</td>
        </tr>
    </table>
</div>
<!--
@if($employee->payment_method === 'paypal' && $employee->paypal_id)
<div class="pay-footer">
    <div class="pay-label">Pay via PayPal</div>
    <div class="pay-row">
        <div class="pay-info">
            <p>Send to: <strong>{{ $employee->paypal_id }}</strong></p>
            <p style="margin-top:2px;font-size:9.5px;color:#6b7280;">paypal.me/{{ $employee->paypal_id }}/{{ number_format($totalPay, 2) }}</p>
        </div>
        <div class="pay-cta">
            <a href="https://paypal.me/{{ urlencode($employee->paypal_id) }}/{{ number_format($totalPay, 2) }}" class="pay-btn">
                Pay ${{ number_format($totalPay, 2) }} via PayPal &rarr;
            </a>
        </div>
    </div>
</div>

@elseif($employee->payment_method === 'wise' && $employee->wise_id)
<div class="pay-footer wise">
    <div class="pay-label wise">Pay via Wise</div>
    <div class="pay-row">
        <div class="pay-info">
            <p>Send to: <strong>{{ $employee->wise_id }}</strong></p>
            <p style="margin-top:2px;font-size:9.5px;color:#6b7280;">wise.com/pay/me/{{ $employee->wise_id }}</p>
        </div>
        <div class="pay-cta">
            <a href="https://wise.com/pay/me/{{ urlencode($employee->wise_id) }}" class="pay-btn wise">
                Pay ${{ number_format($totalPay, 2) }} via Wise &rarr;
            </a>
        </div>
    </div>
</div>
@endif
-->

<div class="doc-footer">
    <div class="doc-footer-left">Generated by goTrackr &mdash; Confidential</div>
    <div class="doc-footer-right">{{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}</div>
</div>

</body>
</html>