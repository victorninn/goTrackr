@extends('layouts.app')

@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your account preferences')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- ── Change Password ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Change Password</h2>
                <p class="text-xs text-gray-400">Keep your account secure</p>
            </div>
        </div>

        @if(session('success_password'))
        <div class="mx-6 mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success_password') }}
        </div>
        @endif

        <form method="POST" action="{{ route('employee.settings.password') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Current Password</label>
                <input type="password" name="current_password"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">New Password</label>
                <input type="password" name="password"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="pt-1">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- ── Payment Settings ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Payment Settings</h2>
                <p class="text-xs text-gray-400">How you prefer to receive payments</p>
            </div>
        </div>

        @if(session('success_payment'))
        <div class="mx-6 mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success_payment') }}
        </div>
        @endif

        <form method="POST" action="{{ route('employee.settings.payment') }}" class="px-6 py-5">
            @csrf

            {{-- Method selector --}}
            <p class="text-xs font-medium text-gray-500 mb-3">Select Payment Method</p>
            <div class="grid grid-cols-3 gap-3 mb-5" id="payment-cards">

                {{-- PayPal --}}
                <label class="payment-card cursor-pointer" data-method="paypal">
                    <input type="radio" name="payment_method" value="paypal" class="sr-only"
                        {{ old('payment_method', $user->payment_method) === 'paypal' ? 'checked' : '' }}>
                    <div class="border-2 rounded-xl p-4 text-center transition-all
                        {{ old('payment_method', $user->payment_method) === 'paypal' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}"
                        id="card-paypal">
                        <div class="text-2xl mb-1">🅿️</div>
                        <div class="text-xs font-semibold text-gray-700">PayPal</div>
                        <div class="text-xs text-gray-400 mt-0.5">Instant</div>
                    </div>
                </label>

                {{-- Wise --}}
                <label class="payment-card cursor-pointer" data-method="wise">
                    <input type="radio" name="payment_method" value="wise" class="sr-only"
                        {{ old('payment_method', $user->payment_method) === 'wise' ? 'checked' : '' }}>
                    <div class="border-2 rounded-xl p-4 text-center transition-all
                        {{ old('payment_method', $user->payment_method) === 'wise' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}"
                        id="card-wise">
                        <div class="text-2xl mb-1">💚</div>
                        <div class="text-xs font-semibold text-gray-700">Wise</div>
                        <div class="text-xs text-gray-400 mt-0.5">Low fees</div>
                    </div>
                </label>

                {{-- Bank — greyed out (coming soon) --}}
                <div class="opacity-50 cursor-not-allowed" title="Coming soon">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center bg-gray-50">
                        <div class="text-2xl mb-1">🏦</div>
                        <div class="text-xs font-semibold text-gray-400">Bank Transfer</div>
                        <div class="text-xs text-gray-300 mt-0.5">Coming soon</div>
                    </div>
                </div>

            </div>

            {{-- PayPal ID field --}}
            <div id="field-paypal" class="{{ old('payment_method', $user->payment_method) === 'paypal' ? '' : 'hidden' }} mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">PayPal Email / ID</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <input type="text" name="paypal_id" value="{{ old('paypal_id', $user->paypal_id) }}"
                        placeholder="your@paypal.email"
                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="mt-1 text-xs text-gray-400">Clients will see a "Pay via PayPal" link on your invoice.</p>
            </div>

            {{-- Wise ID field --}}
            <div id="field-wise" class="{{ old('payment_method', $user->payment_method) === 'wise' ? '' : 'hidden' }} mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">Wise Email / Username</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <input type="text" name="wise_id" value="{{ old('wise_id', $user->wise_id) }}"
                        placeholder="your@wise.email"
                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="mt-1 text-xs text-gray-400">Clients will see a "Pay via Wise" link on your invoice.</p>
            </div>

            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Save Payment Settings
            </button>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards     = document.querySelectorAll('.payment-card');
    const fieldPP   = document.getElementById('field-paypal');
    const fieldWise = document.getElementById('field-wise');

    function updateUI(method) {
        // Reset card styles
        document.getElementById('card-paypal').className =
            'border-2 rounded-xl p-4 text-center transition-all border-gray-200 hover:border-blue-300';
        document.getElementById('card-wise').className =
            'border-2 rounded-xl p-4 text-center transition-all border-gray-200 hover:border-green-300';

        fieldPP.classList.add('hidden');
        fieldWise.classList.add('hidden');

        if (method === 'paypal') {
            document.getElementById('card-paypal').className =
                'border-2 rounded-xl p-4 text-center transition-all border-blue-500 bg-blue-50';
            fieldPP.classList.remove('hidden');
        } else if (method === 'wise') {
            document.getElementById('card-wise').className =
                'border-2 rounded-xl p-4 text-center transition-all border-green-500 bg-green-50';
            fieldWise.classList.remove('hidden');
        }
    }

    cards.forEach(card => {
        const radio = card.querySelector('input[type=radio]');
        card.addEventListener('click', function () {
            radio.checked = true;
            updateUI(card.dataset.method);
        });
    });
});
</script>

@endsection
