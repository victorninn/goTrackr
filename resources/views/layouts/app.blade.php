<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TimeTracker') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-64 bg-blue-900 text-white flex flex-col flex-shrink-0 transition-all duration-300">

        {{-- Logo / Company --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-blue-800">
            @php $company = auth()->user()->company; @endphp
            @if($company && $company->logo)
                <img src="{{ Storage::url($company->logo) }}" alt="Logo" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
            @else
                <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr($company->name ?? 'T', 0, 1)) }}
                </div>
            @endif
            <div class="overflow-hidden">
                <p class="text-xs text-blue-300 leading-none">Company</p>
                <p class="text-sm font-semibold truncate leading-tight mt-0.5">{{ $company->name ?? 'TimeTracker' }}</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-4 overflow-y-auto">
            <div class="px-3 space-y-0.5">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
  <rect x="3" y="3" width="7" height="8" rx="2"/>
  <rect x="14" y="3" width="7" height="5" rx="2"/>
  <rect x="14" y="12" width="7" height="9" rx="2"/>
  <rect x="3" y="15" width="7" height="6" rx="2"/>
</svg>
                    Dashboard
                </a>

                {{-- Employee-only --}}
                @if(auth()->user()->isEmployee())
                <a href="{{ route('logs.my') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('logs.my') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Logs
                </a>
                @endif

                {{-- Admin + Superadmin --}}
                @if(auth()->user()->hasAnyRole(['superadmin','admin']))

                <div class="pt-3 pb-1 px-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Management</p>
                </div>

                <a href="{{ route('employees.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('employees.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="9" cy="7" r="3"/>
                        <path d="M3 21v-1a6 6 0 0 1 6-6h0"/>
                        <circle cx="17" cy="10" r="2.5"/>
                        <path d="M13.5 21v-1a3.5 3.5 0 0 1 7 0v1"/>
                    </svg>
                    Employees
                </a>

                <a href="{{ route('logs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('logs.index') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="13" r="7"/>
                        <path d="M12 10v3.5l2.5 1.5"/>
                        <path d="M9.5 3h5"/>
                        <path d="M12 3v3"/>
                    </svg>
                    Time Logs
                </a>

                <a href="{{ route('payroll.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('payroll.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                   <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <path d="M2 10h20"/>
                        <path d="M6 15h3"/>
                        <path d="M12 15h3"/>
                        <circle cx="18" cy="15" r="1.5" fill="currentColor" stroke="none"/>
                    </svg>
                    Payroll
                </a>

                <a href="{{ route('companies.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('companies.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M5 21V7l7-4 7 4v14"/>
                        <rect x="9" y="14" width="6" height="7" rx="1"/>
                        <path d="M9 10h.01M15 10h.01M9 7h.01M15 7h.01"/>
                    </svg>
                    Companies
                </a>

                @endif

                {{-- Superadmin only --}}
                @if(auth()->user()->isSuperAdmin())

                <div class="pt-3 pb-1 px-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Super Admin</p>
                </div>

                <a href="{{ route('admins.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('admins.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Manage Admins
                </a>

                @endif

            </div>
        </nav>

        {{-- User info at bottom --}}
        <div class="border-t border-blue-800 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-medium truncate leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-400 mt-0.5">{{ auth()->user()->role->label }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full text-left flex items-center gap-2 text-xs text-blue-300 hover:text-white transition-colors py-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOP BAR --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400 mt-0.5">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500" id="current-datetime"></span>
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')

        </main>
    </div>
</div>

<script>
    // Live clock in top bar
    function updateClock() {
        const now = new Date();
        document.getElementById('current-datetime').textContent =
            now.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric' }) +
            ' · ' +
            now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

@yield('scripts')

</body>
</html>