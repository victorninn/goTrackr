@extends('layouts.app')

@section('page-title', 'My Dashboard')
@section('page-subtitle', 'Track your time')

@section('content')

{{-- Clock In/Out Card --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Clock Widget --}}
    <div class="lg:col-span-1 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl shadow-lg p-6 text-white">
        <p class="text-blue-200 text-sm font-medium mb-1">Today</p>
        <p class="text-2xl font-bold mb-1" id="today-date">{{ now()->format('l, M d') }}</p>
        <p class="text-4xl font-mono font-bold mb-6" id="live-clock">--:--:--</p>

        @if($activeLog)
            {{-- Active clock-in state --}}
            <div class="bg-white/10 rounded-xl p-4 mb-5">
                <p class="text-blue-200 text-xs mb-1">Clocked in at</p>
                <p class="text-xl font-semibold">{{ \Carbon\Carbon::parse($activeLog->clock_in)->format('h:i A') }}</p>
                <p class="text-blue-200 text-xs mt-2">Working on</p>

<form method="POST" action="http://127.0.0.1:8000/logs/update-active-description">
    <input type="hidden" name="_token" value="TCrxG54y8ALwKPz8vOan1flc5Dr3UQDARmKVEbUw" autocomplete="off">

    <textarea 
        name="description" 
        rows="2" 
        class="w-full bg-white/10 text-white placeholder-blue-300 border border-white/20 rounded-xl px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-white/40 resize-none" 
        placeholder="What are you working on?"
    ></textarea>

    <div class="flex justify-end mt-2">
        <button 
            type="submit" 
            class="bg-blue-400 hover:bg-yellow-500 text-white text-xs font-semibold py-2 px-4 rounded-lg transition">
            Update Task
        </button>
    </div>
</form>
                <p class="text-blue-200 text-xs mt-2">Elapsed time</p>
                <p class="text-2xl font-mono font-bold text-yellow-300" id="elapsed-timer">00:00:00</p>
            </div>
            <form method="POST" action="{{ route('clock.out') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-xl transition-colors shadow-md">
                    🔴 Clock Out
                </button>
            </form>
        @else
            {{-- Not clocked in --}}
            <div class="bg-white/10 rounded-xl p-4 mb-5">
                <p class="text-blue-200 text-sm text-center">You are not clocked in</p>
            </div>
            <form method="POST" action="{{ route('clock.in') }}">
                @csrf
                <textarea
                    name="description"
                    rows="2"
                    placeholder="What are you working on? (optional)"
                    class="w-full bg-white/10 text-white placeholder-blue-300 border border-white/20 rounded-xl px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-white/40 resize-none"
                ></textarea>
                <button type="submit"
                    class="w-full bg-green-400 hover:bg-green-500 text-white font-semibold py-3 rounded-xl transition-colors shadow-md">
                    🟢 Clock In
                </button>
            </form>
        @endif
    </div>

    {{-- Stats --}}
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Hours This Week</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($weeklyHours, 2) }}</p>
            <p class="text-sm text-gray-400 mt-1">hours</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Hours This Month</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($monthlyHours, 2) }}</p>
            <p class="text-sm text-gray-400 mt-1">hours</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Hourly Rate</p>
                <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">${{ number_format(auth()->user()->hourly_rate, 2) }}</p>
            <p class="text-sm text-gray-400 mt-1">per hour</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Est. Monthly Pay</p>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">${{ number_format($monthlyHours * auth()->user()->hourly_rate, 2) }}</p>
            <p class="text-sm text-gray-400 mt-1">this month</p>
        </div>

    </div>
</div>

{{-- Export Buttons --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('logs.export.my', ['type'=>'weekly']) }}"
        class="flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Weekly PDF
    </a>
    <a href="{{ route('logs.export.my', ['type'=>'monthly']) }}"
        class="flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Monthly PDF
    </a>
    <a href="{{ route('logs.my') }}" class="text-sm text-blue-600 hover:underline ml-2">View full history →</a>
</div>

{{-- Recent Logs Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Recent Logs</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock In</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clock Out</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Hours</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $log->date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                    <td class="px-6 py-3 text-gray-600">
                        {{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3 font-medium text-gray-800">
                        {{ $log->total_hours ? $log->total_hours . ' hrs' : '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate" title="{{ $log->description }}">
                        {{ $log->description ?: '—' }}
                    </td>
                    <td class="px-6 py-3">
                        @if($log->clock_out)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-gray-600">Completed</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Active
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">No time logs yet. Clock in to start!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Live clock
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent =
            now.toLocaleTimeString('en-US', { hour12: false });
    }
    setInterval(updateClock, 1000);
    updateClock();

    @if($activeLog)
    // Elapsed timer
    const clockInTime = new Date();
    const timeParts = "{{ $activeLog->clock_in }}".split(':');
    clockInTime.setHours(parseInt(timeParts[0]), parseInt(timeParts[1]), parseInt(timeParts[2] || 0), 0);

    // Handle if clock_in was yesterday (edge case)
    if (clockInTime > new Date()) {
        clockInTime.setDate(clockInTime.getDate() - 1);
    }

    function updateElapsed() {
        const now = new Date();
        const diff = Math.floor((now - clockInTime) / 1000);
        const h = Math.floor(diff / 3600).toString().padStart(2, '0');
        const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
        const s = (diff % 60).toString().padStart(2, '0');
        const el = document.getElementById('elapsed-timer');
        if (el) el.textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateElapsed, 1000);
    updateElapsed();
    @endif
</script>
@endsection