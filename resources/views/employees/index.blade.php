@extends('layouts.app')

@section('page-title', 'Employees')
@section('page-subtitle', 'Manage your workforce')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $employees->total() }} employees total</p>
    <a href="{{ route('employees.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Add Employee
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
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

@endsection
