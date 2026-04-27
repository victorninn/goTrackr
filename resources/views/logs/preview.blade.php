@extends('layouts.app')

@section('page-title', ucfirst($type) . ' Invoice Preview')
@section('page-subtitle', $label)

@section('content')

{{-- Tab switcher --}}
<div class="flex flex-wrap items-center gap-2 mb-5">

    {{-- Weekly tab with week picker --}}
    <div class="relative">
        <a href="{{ route('logs.preview', ['type' => 'weekly', 'week' => request('week')]) }}"
            class="px-5 py-2 rounded-full text-sm font-medium transition-colors
            {{ $type === 'weekly' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            📅 Weekly
        </a>
    </div>

    @if($type === 'weekly')
    <input type="week"
        value="{{ request('week', now()->format('Y-\WW')) }}"
        onchange="window.location.href = '{{ route('logs.preview', ['type' => 'weekly']) }}&week=' + this.value"
        class="border border-gray-200 rounded-full px-3 py-1.5 text-sm text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 cursor-pointer" />
    @endif

    {{-- Monthly tab with month picker --}}
    <div class="relative">
        <a href="{{ route('logs.preview', ['type' => 'monthly', 'month' => request('month')]) }}"
            class="px-5 py-2 rounded-full text-sm font-medium transition-colors
            {{ $type === 'monthly' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            📆 Monthly
        </a>
    </div>

    @if($type === 'monthly')
    <input type="month"
        value="{{ request('month', now()->format('Y-m')) }}"
        onchange="window.location.href = '{{ route('logs.preview', ['type' => 'monthly']) }}&month=' + this.value"
        class="border border-gray-200 rounded-full px-3 py-1.5 text-sm text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 cursor-pointer" />
    @endif

    <div class="ml-auto flex items-center gap-2">
        {{-- Copy Link --}}
        <button id="btn-share" onclick="generateShareLink('{{ $type }}')"
            class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.7"/>
            </svg>
            <span id="share-btn-text">Copy Shareable Link</span>
        </button>

        {{-- Export PDF --}}
        <a href="{{ route('logs.export.my', ['type' => $type, 'week' => request('week'), 'month' => request('month')]) }}"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Export PDF
        </a>
    </div>
</div>

{{-- Share link popup --}}
<div id="share-popup" class="hidden mb-4 px-4 py-3 bg-indigo-50 border border-indigo-200 rounded-xl flex items-center gap-3">
    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.7"/>
    </svg>
    <input id="share-link-input" type="text" readonly
        class="flex-1 bg-transparent text-sm text-indigo-700 font-mono outline-none truncate" />
    <button onclick="copyLink()" class="text-xs text-indigo-600 font-medium hover:underline shrink-0">Copy</button>
    <span id="copied-badge" class="hidden text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full">Copied!</span>
</div>

{{-- Invoice preview card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Invoice header --}}
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest mb-1">Invoice Preview</p>
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                <p class="text-blue-200 text-sm mt-0.5">{{ $user->email }}</p>
                @if($user->company)
                    <p class="text-blue-100 text-xs mt-1">{{ $user->company->name }}</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-blue-200 uppercase tracking-wide font-medium mb-1">Period</div>
                <div class="text-sm font-semibold">{{ $label }}</div>
                <div class="mt-3 text-xs text-blue-200">Rate: ${{ number_format($user->hourly_rate, 2) }}/hr</div>
            </div>
        </div>
    </div>

    {{-- Log table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock In</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock Out</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Hours</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                @php $amount = $log->total_hours ? $log->total_hours * $user->hourly_rate : 0; @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $log->date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $log->description ?: '—' }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : '—' }}</td>
                    <td class="px-6 py-3 text-right font-medium text-gray-700">{{ $log->total_hours ?? '—' }}</td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-800">${{ number_format($amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No time logs for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="border-t border-gray-100 px-8 py-5">
        <div class="flex justify-end">
            <div class="w-64 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Total Hours</span>
                    <span class="font-medium">{{ number_format($totalHours, 2) }} hrs</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Hourly Rate</span>
                    <span class="font-medium">${{ number_format($user->hourly_rate, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 border-t border-gray-200 pt-2 mt-2">
                    <span>Total Due</span>
                    <span class="text-blue-600">${{ number_format($totalPay, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment links --}}
    @if($user->payment_method === 'paypal' && $user->paypal_id)
    <div class="border-t border-gray-100 px-8 py-4 bg-blue-50 flex items-center gap-3">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
        </svg>
        <span class="text-sm text-gray-600">Pay via PayPal to:</span>
        <a href="https://paypal.me/{{ $user->paypal_id }}" target="_blank"
            class="text-sm font-semibold text-blue-600 hover:underline">{{ $user->paypal_id }}</a>
        <a href="https://paypal.me/{{ $user->paypal_id }}" target="_blank"
            class="ml-auto flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition-colors">
            Pay with PayPal →
        </a>
    </div>
    @elseif($user->payment_method === 'wise' && $user->wise_id)
    <div class="border-t border-gray-100 px-8 py-4 bg-green-50 flex items-center gap-3">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
        </svg>
        <span class="text-sm text-gray-600">Pay via Wise to:</span>
        <span class="text-sm font-semibold text-green-700">{{ $user->wise_id }}</span>
        <a href="https://wise.com/pay" target="_blank"
            class="ml-auto flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition-colors">
            Pay with Wise →
        </a>
    </div>
    @endif

</div>

<script>
function generateShareLink(type) {
    const btn  = document.getElementById('btn-share');
    const text = document.getElementById('share-btn-text');
    text.textContent = 'Generating…';
    btn.disabled = true;

    fetch('{{ route('logs.share') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ type })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('share-link-input').value = data.link;
        document.getElementById('share-popup').classList.remove('hidden');
        text.textContent = 'Copy Shareable Link';
        btn.disabled = false;
    })
    .catch(() => {
        text.textContent = 'Error, try again';
        btn.disabled = false;
    });
}

function copyLink() {
    const input = document.getElementById('share-link-input');
    navigator.clipboard.writeText(input.value).then(() => {
        const badge = document.getElementById('copied-badge');
        badge.classList.remove('hidden');
        setTimeout(() => badge.classList.add('hidden'), 2000);
    });
}
</script>

@endsection
