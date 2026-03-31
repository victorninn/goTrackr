@extends('layouts.app')

@section('page-title', 'Employees')
@section('page-subtitle', 'Manage your workforce')

@section('content')

<div class="flex items-center justify-between mb-6 gap-3">
    <p class="text-sm text-gray-500">{{ $employees->total() }} employees total</p>
    <a href="{{ route('employees.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="hidden sm:inline">Add Employee</span>
        <span class="sm:hidden">Add</span>
    </a>
</div>

{{-- ===================== DESKTOP TABLE (md and up) ===================== --}}
<div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Hourly Rate</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $employee->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $employee->email }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $employee->company->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-800">${{ number_format($employee->hourly_rate, 2) }}/hr</td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $employee->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('employees.edit', $employee) }}"
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                  onsubmit="return confirm('Remove {{ $employee->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                        No employees yet.
                        <a href="{{ route('employees.create') }}" class="text-blue-600 hover:underline ml-1">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employees->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $employees->links() }}</div>
    @endif
</div>

{{-- ===================== MOBILE CARD LIST (below md) ===================== --}}
<div class="md:hidden space-y-3">
    @forelse($employees as $employee)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-start justify-between gap-3">
            {{-- Avatar + name + email --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ $employee->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $employee->email }}</p>
                </div>
            </div>
            {{-- Hourly rate badge --}}
            <span class="flex-shrink-0 text-sm font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">
                ${{ number_format($employee->hourly_rate, 2) }}/hr
            </span>
        </div>

        {{-- Company + Joined --}}
        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ $employee->company->name ?? '—' }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Joined {{ $employee->created_at->format('M d, Y') }}
            </span>
        </div>

        {{-- Actions --}}
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
            <a href="{{ route('employees.edit', $employee) }}"
               class="flex-1 text-center text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-2 rounded-lg hover:bg-blue-50 border border-blue-100 transition-colors">
                Edit
            </a>
            <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                  onsubmit="return confirm('Remove {{ $employee->name }}?')"
                  class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full text-center text-red-600 hover:text-red-800 text-xs font-medium px-3 py-2 rounded-lg hover:bg-red-50 border border-red-100 transition-colors">
                    Remove
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-10 text-center text-gray-400">
        No employees yet.
        <a href="{{ route('employees.create') }}" class="text-blue-600 hover:underline ml-1">Add one →</a>
    </div>
    @endforelse

    @if($employees->hasPages())
    <div class="pt-2">{{ $employees->links() }}</div>
    @endif
</div>

@endsection