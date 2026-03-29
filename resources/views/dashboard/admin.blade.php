@extends('layouts.app')

@section('page-title', 'Admin Dashboard')
@section('page-subtitle', $company->name ?? 'Your Company')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Employees</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalEmployees }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Hours This Week</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($weeklyHours, 2) }}<span class="text-base font-normal text-gray-400 ml-1">hrs</span></p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Hours This Month</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($monthlyHours, 2) }}<span class="text-base font-normal text-gray-400 ml-1">hrs</span></p>
    </div>

</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl p-4 text-center transition-colors shadow-sm">
        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        <p class="text-sm font-medium">Add Employee</p>
    </a>
    <a href="{{ route('payroll.index') }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 rounded-xl p-4 text-center transition-colors shadow-sm">
        <svg class="w-6 h-6 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium">Payroll</p>
    </a>
    <a href="{{ route('logs.export', ['type'=>'weekly']) }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 rounded-xl p-4 text-center transition-colors shadow-sm">
        <svg class="w-6 h-6 mx-auto mb-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        <p class="text-sm font-medium">Weekly Export</p>
    </a>
    <a href="{{ route('logs.export', ['type'=>'monthly']) }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 rounded-xl p-4 text-center transition-colors shadow-sm">
        <svg class="w-6 h-6 mx-auto mb-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        <p class="text-sm font-medium">Monthly Export</p>
    </a>
</div>

{{-- Recent Logs --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Recent Employee Logs</h2>
        <a href="{{ route('logs.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Employee</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock In</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock Out</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Hours</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $log->user->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $log->date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td class="px-6 py-3 text-gray-600">
                        {{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3 font-medium">{{ $log->total_hours ? $log->total_hours . 'h' : '—' }}</td>
                    <td class="px-6 py-3">
                        @if($log->clock_out)
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">Done</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Active
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">No logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
