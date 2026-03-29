@extends('layouts.app')

@section('page-title', 'Companies')
@section('page-subtitle', 'Manage registered companies')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $companies->total() }} companies</p>
    @if(auth()->user()->isSuperAdmin())
    <a href="{{ route('companies.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Add Company
    </a>
    @endif
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Users</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Created</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($companies as $company)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="Logo"
                                    class="w-9 h-9 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($company->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $company->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $company->users_count ?? $company->users()->count() }}</td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $company->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('companies.edit', $company) }}"
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                Edit
                            </a>
                            @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('companies.destroy', $company) }}"
                                  onsubmit="return confirm('Delete {{ $company->name }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">No companies yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $companies->links() }}</div>
    @endif
</div>

@endsection
