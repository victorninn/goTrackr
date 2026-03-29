@extends('layouts.app')

@section('page-title', 'My Time Logs')
@section('page-subtitle', 'Your complete time history')

@section('content')

{{-- Filter & Export --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('logs.my') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filter
        </button>
        <a href="{{ route('logs.my') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">Clear</a>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('logs.export.my', ['type'=>'weekly']) }}"
    class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Weekly PDF
</a>
            <a href="{{ route('logs.export.my', ['type'=>'monthly']) }}"
    class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Monthly PDF
</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock In</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock Out</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Hours</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Est. Pay</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $log->date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-700">{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td class="px-6 py-3 text-gray-700">
                        {{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3 font-semibold text-gray-800">
                        {{ $log->total_hours ? $log->total_hours . ' hrs' : '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-700">
                        @if($log->total_hours)
                            ${{ number_format($log->total_hours * auth()->user()->hourly_rate, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($log->clock_out)
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-gray-600">Completed</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Active
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No time logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
