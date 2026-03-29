@extends('layouts.app')

@section('page-title', 'Super Admin Dashboard')
@section('page-subtitle', 'Full system overview')

@section('content')

{{-- Stats Row --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalUsers }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Admins</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAdmins }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Employees</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalEmployees }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Companies</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalCompanies }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
    <a href="{{ route('admins.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl p-5 flex items-center gap-4 transition-colors shadow-sm">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <div>
            <p class="font-semibold">Create Admin</p>
            <p class="text-blue-200 text-xs mt-0.5">Add a new admin user</p>
        </div>
    </a>
    <a href="{{ route('companies.create') }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 rounded-xl p-5 flex items-center gap-4 transition-colors shadow-sm">
        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <div>
            <p class="font-semibold">New Company</p>
            <p class="text-gray-400 text-xs mt-0.5">Register a company</p>
        </div>
    </a>
    <a href="{{ route('logs.export', ['type'=>'monthly']) }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 rounded-xl p-5 flex items-center gap-4 transition-colors shadow-sm">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div>
            <p class="font-semibold">Export All Logs</p>
            <p class="text-gray-400 text-xs mt-0.5">Monthly CSV export</p>
        </div>
    </a>
</div>

{{-- Recent Logs --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Recent Time Logs</h2>
        <a href="{{ route('logs.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Employee</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock In</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock Out</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Hours</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $log->user->name }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $log->user->company->name ?? '—' }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $log->date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td class="px-6 py-3 text-gray-600">
                        @if($log->clock_out)
                            {{ \Carbon\Carbon::parse($log->clock_out)->format('h:i A') }}
                        @else
                            <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Active
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($log->total_hours)
                            <span class="font-medium text-gray-800">{{ $log->total_hours }}h</span>
                        @else
                            <span class="text-gray-400">—</span>
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
