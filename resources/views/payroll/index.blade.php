@extends('layouts.app')

@section('page-title', 'Payroll Summary')
@section('page-subtitle', 'hourly rate × total hours')

@section('content')

{{-- Period Selector --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('payroll.index') }}" class="flex flex-wrap items-end gap-4">

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Period</label>
            <select name="period" id="period-select" onchange="togglePeriod()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="weekly"  {{ $period === 'weekly'  ? 'selected' : '' }}>Weekly</option>
            </select>
        </div>

        <div id="month-picker" class="{{ $period === 'weekly' ? 'hidden' : '' }}">
            <label class="block text-xs font-medium text-gray-500 mb-1">Month</label>
            <input type="month" name="month" value="{{ $month }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div id="week-picker" class="{{ $period === 'monthly' ? 'hidden' : '' }}">
            <label class="block text-xs font-medium text-gray-500 mb-1">Week starting</label>
            <input type="date" name="week" value="{{ $week }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            View
        </button>

        <div class="ml-auto">
            <a href="{{ route('payroll.export', ['period'=>$period,'month'=>$month,'week'=>$week]) }}"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Payroll CSV
            </a>
        </div>

    </form>
</div>

{{-- Period label --}}
<div class="mb-4 flex items-center gap-2">
    <span class="text-sm text-gray-500">Showing payroll for:</span>
    <span class="text-sm font-semibold text-gray-800 bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
        @if($period === 'weekly')
            Week of {{ $start->format('M d') }} – {{ $end->format('M d, Y') }}
        @else
            {{ $start->format('F Y') }}
        @endif
    </span>
</div>

{{-- Totals Summary --}}
@php
    $grandHours  = $employees->sum('total_hours');
    $grandSalary = $employees->sum('total_salary');
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Employees</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $employees->count() }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Hours</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($grandHours, 2) }}<span class="text-base font-normal text-gray-400 ml-1">hrs</span></p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Payroll</p>
        <p class="text-3xl font-bold text-green-700 mt-1">${{ number_format($grandSalary, 2) }}</p>
    </div>
</div>

{{-- Payroll Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Employee</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</th>
                    @endif
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Hours</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Hourly Rate</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Salary</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $emp)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($emp['name'], 0, 2)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $emp['name'] }}</span>
                        </div>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $emp['company'] ?? '—' }}</td>
                    @endif
                    <td class="px-6 py-4 text-right font-medium text-gray-800">{{ number_format($emp['total_hours'], 2) }}</td>
                    <td class="px-6 py-4 text-right text-gray-600">${{ number_format($emp['hourly_rate'], 2) }}</td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-lg font-bold text-green-700">${{ number_format($emp['total_salary'], 2) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No payroll data for this period.</td></tr>
                @endforelse
            </tbody>
            @if($employees->count() > 0)
            <tfoot>
                <tr class="bg-gray-50 border-t-2 border-gray-200">
                    <td class="px-6 py-4 font-bold text-gray-700" colspan="{{ auth()->user()->isSuperAdmin() ? 2 : 1 }}">Total</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800">{{ number_format($grandHours, 2) }}</td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4 text-right font-bold text-green-700 text-lg">${{ number_format($grandSalary, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
function togglePeriod() {
    const period = document.getElementById('period-select').value;
    document.getElementById('month-picker').classList.toggle('hidden', period !== 'monthly');
    document.getElementById('week-picker').classList.toggle('hidden', period !== 'weekly');
}
</script>
@endsection
