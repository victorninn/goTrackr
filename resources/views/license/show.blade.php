@extends('layouts.app')

@section('page-title', 'License')
@section('page-subtitle', auth()->user()->isSuperAdmin() ? 'Generate and manage license keys' : 'Activate your license plan')

@section('content')

@php
    $user       = auth()->user();
    $license    = $company?->license;
    $planName   = $license ? ucfirst($license->name) : 'Free';
    $limit      = $company ? $company->employeeLimit() : 3;
    $used       = $company ? $company->employeeCount() : 0;
    $pct        = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 100;
    $barColor   = $pct >= 100 ? 'bg-red-500' : ($pct >= 75 ? 'bg-yellow-400' : 'bg-blue-500');
    $badgeColor = match($planName) {
        'Basic'    => 'bg-blue-100 text-blue-700',
        'Business' => 'bg-purple-100 text-purple-700',
        default    => 'bg-gray-100 text-gray-600',
    };
@endphp

{{-- Flash messages --}}
@if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if($errors->has('delete'))
    <div class="mb-5 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
        {{ $errors->first('delete') }}
    </div>
@endif

<div class="max-w-3xl space-y-6">

    {{-- ============================================================ --}}
    {{-- SUPERADMIN: Key Generator + Key Table                        --}}
    {{-- ============================================================ --}}
    @if($user->isSuperAdmin())

    {{-- Generate Keys --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Generate License Keys</h2>
        <p class="text-xs text-gray-400 mb-5">Keys are generated in <span class="font-mono">XXXX-XXXX-XXXX-XXXX</span> format and can be handed to business owners.</p>

        <form method="POST" action="{{ route('license.generate') }}" class="flex flex-wrap items-end gap-4">
            @csrf

            <div class="flex-1 min-w-[160px]">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Plan</label>
                <select name="license_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($licenses as $lic)
                        <option value="{{ $lic->id }}">{{ ucfirst($lic->name) }} ({{ $lic->employee_limit }} employees)</option>
                    @endforeach
                </select>
            </div>

            <div class="w-28">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Quantity</label>
                <input type="number" name="quantity" value="1" min="1" max="50" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">
                Generate Keys
            </button>
        </form>
    </div>

    {{-- Master Keys Info --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800 mb-2">Master Keys (Super Admin only — never expire)</p>
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-amber-700 w-16">Basic</span>
                        <code class="text-xs font-mono bg-amber-100 border border-amber-300 px-2 py-1 rounded select-all">MASTER-BASIC-GOTRACKR-2024</code>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-amber-700 w-16">Business</span>
                        <code class="text-xs font-mono bg-amber-100 border border-amber-300 px-2 py-1 rounded select-all">MASTER-BUSINESS-GOTRACKR-2024</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Generated Keys Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Generated Keys</h2>
            <span class="text-xs text-gray-400">{{ $licenseKeys->count() }} total</span>
        </div>

        @if($licenseKeys->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400">No keys generated yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Key</th>
                            <th class="px-6 py-3 text-left">Plan</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Used At</th>
                            <th class="px-6 py-3 text-left"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($licenseKeys as $lk)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3">
                                <code class="font-mono text-xs text-gray-700 select-all">{{ $lk->license_key }}</code>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $badge = match($lk->license->name) {
                                        'basic'    => 'bg-blue-100 text-blue-700',
                                        'business' => 'bg-purple-100 text-purple-700',
                                        default    => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ ucfirst($lk->license->name) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if($lk->is_used)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span> Used
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Available
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-400">
                                {{ $lk->used_at ? $lk->used_at->format('M d, Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                @if(! $lk->is_used)
                                <form method="POST" action="{{ route('license.key.destroy', $lk) }}"
                                    onsubmit="return confirm('Delete this key?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @endif {{-- end superadmin --}}

    {{-- ============================================================ --}}
    {{-- ALL USERS: Current Plan Status                               --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
            {{ $user->isSuperAdmin() ? 'Your Company Plan' : 'Current Plan' }}
        </h2>

        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                    {{ $planName }} Plan
                </span>
                @if($company?->license_key)
                    <p class="text-xs text-gray-400 mt-1.5 font-mono">{{ $company->license_key }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-800">{{ $used }} <span class="text-base font-normal text-gray-400">/ {{ $limit }}</span></p>
                <p class="text-xs text-gray-400">employees used</p>
            </div>
        </div>

        <div class="w-full bg-gray-100 rounded-full h-2.5">
            <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1.5">{{ $pct }}% of employee slots used</p>

        @if($pct >= 100)
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                </svg>
                Employee limit reached. Enter a new license key to upgrade.
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- ADMIN: Activate Key Form                                     --}}
    {{-- ============================================================ --}}
    @if(! $user->isSuperAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Activate License Key</h2>
        <p class="text-xs text-gray-400 mb-5">Enter the key provided to you to upgrade your plan.</p>

        @if($errors->has('license_key'))
            <div class="mb-5 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                {{ $errors->first('license_key') }}
            </div>
        @endif

        <form method="POST" action="{{ route('license.activate') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">License Key</label>
                <input
                    type="text"
                    name="license_key"
                    value="{{ old('license_key') }}"
                    placeholder="XXXX-XXXX-XXXX-XXXX"
                    autocomplete="off"
                    spellcheck="false"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                >
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                Activate Key
            </button>
        </form>
    </div>
    @endif

</div>

@endsection
