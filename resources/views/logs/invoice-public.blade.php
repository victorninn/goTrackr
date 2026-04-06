<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice – {{ $user->name }} – {{ $label }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', system-ui, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">

<div class="max-w-2xl mx-auto">

    {{-- Toolbar (hidden on print) --}}
    <div class="no-print flex items-center justify-between mb-5">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="13" r="7"/><path d="M12 10v3.5l2.5 1.5"/><path d="M9.5 3h5"/><path d="M12 3v3"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-gray-600">goTrackr</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()"
                class="flex items-center gap-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Main invoice card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- Header stripe --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-7 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-2">Invoice</div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ $user->name }}</h1>
                    <p class="text-blue-200 text-sm mt-0.5">{{ $user->email }}</p>
                    @if($user->company)
                        <p class="text-blue-100 text-xs mt-1">{{ $user->company->name }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-xs text-blue-200 uppercase tracking-wide font-semibold">Amount Due</div>
                    <div class="text-3xl font-bold mt-1">${{ number_format($totalPay, 2) }}</div>
                    <div class="text-xs text-blue-200 mt-1">{{ $label }}</div>
                </div>
            </div>
        </div>

        {{-- Meta row --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
            <div class="px-6 py-4">
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Invoice Type</div>
                <div class="text-sm font-semibold text-gray-700 capitalize">{{ $type }}</div>
            </div>
            <div class="px-6 py-4">
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Period</div>
                <div class="text-sm font-semibold text-gray-700">{{ $label }}</div>
            </div>
            <div class="px-6 py-4">
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Hourly Rate</div>
                <div class="text-sm font-semibold text-gray-700">${{ number_format($user->hourly_rate, 2) }}/hr</div>
            </div>
        </div>

        {{-- Line items --}}
        <div class="px-8 py-5">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Date</th>
                        <th class="text-left py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Description</th>
                        <th class="text-right py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Hrs</th>
                        <th class="text-right py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    @php $amt = $log->total_hours ? $log->total_hours * $user->hourly_rate : 0; @endphp
                    <tr>
                        <td class="py-3 text-gray-700 font-medium">{{ $log->date->format('M d, Y') }}</td>
                        <td class="py-3 text-gray-500">
                            {{ $log->description ?: 'Work – ' . \Carbon\Carbon::parse($log->clock_in)->format('h:i A') . ' → ' . ($log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : 'active') }}
                        </td>
                        <td class="py-3 text-right text-gray-700">{{ $log->total_hours ?? '—' }}</td>
                        <td class="py-3 text-right font-semibold text-gray-800">${{ number_format($amt, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-400 text-xs">No time logs for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals block --}}
        <div class="border-t border-gray-100 px-8 py-5 bg-gray-50">
            <div class="flex justify-end">
                <div class="w-56 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Total Hours</span>
                        <span class="font-medium text-gray-700">{{ number_format($totalHours, 2) }} hrs</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Rate</span>
                        <span class="font-medium text-gray-700">${{ number_format($user->hourly_rate, 2) }}/hr</span>
                    </div>
                    <div class="flex justify-between font-bold text-base text-gray-900 border-t border-gray-200 pt-2">
                        <span>Total Due</span>
                        <span class="text-blue-600">${{ number_format($totalPay, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment CTA --}}
        @if($user->payment_method === 'paypal' && $user->paypal_id)
        <div class="border-t-2 border-blue-100 bg-blue-50 px-8 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Pay with PayPal</p>
                    <p class="text-sm text-gray-600">Send to: <span class="font-semibold text-gray-800">{{ $user->paypal_id }}</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Amount: <strong>${{ number_format($totalPay, 2) }}</strong></p>
                </div>
                <a href="https://paypal.me/{{ urlencode($user->paypal_id) }}/{{ number_format($totalPay, 2) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-[#0070ba] hover:bg-[#005ea6] text-white px-6 py-3 rounded-xl font-semibold text-sm transition-colors shadow-sm no-print">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 00-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 00-.556.479l-1.187 7.527h-.99l-.24 1.516a.56.56 0 00.554.647h3.882c.46 0 .85-.334.922-.788l.038-.19.731-4.626.047-.256a.932.932 0 01.921-.788h.58c3.76 0 6.701-1.528 7.561-5.946.36-1.847.174-3.388-.287-4.489z"/>
                    </svg>
                    Pay ${{ number_format($totalPay, 2) }} via PayPal
                </a>
            </div>
        </div>

        @elseif($user->payment_method === 'wise' && $user->wise_id)
        <div class="border-t-2 border-green-100 bg-green-50 px-8 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-1">Pay with Wise</p>
                    <p class="text-sm text-gray-600">Send to: <span class="font-semibold text-gray-800">{{ $user->wise_id }}</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Amount: <strong>${{ number_format($totalPay, 2) }}</strong></p>
                </div>
                <a href="https://wise.com/pay/me/{{ urlencode($user->wise_id) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-[#00b9a0] hover:bg-[#009e8a] text-white px-6 py-3 rounded-xl font-semibold text-sm transition-colors shadow-sm no-print">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.25 8.25l-3 9-3-6-3 6-3-9h1.5l1.5 4.5 3-6 3 6 1.5-4.5z"/>
                    </svg>
                    Pay ${{ number_format($totalPay, 2) }} via Wise
                </a>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="px-8 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400">Generated by <span class="font-semibold text-blue-600">goTrackr</span></span>
            <span class="text-xs text-gray-400">Link expires: {{ $share->expires_at->format('M d, Y') }}</span>
        </div>

    </div>
</div>

</body>
</html>
