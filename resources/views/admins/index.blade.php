@extends('layouts.app')

@section('page-title', 'Manage Admins')
@section('page-subtitle', 'Super admin control panel')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $admins->total() }} admins total</p>
    <a href="{{ route('admins.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Add Admin
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
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Created</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($admins as $admin)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $admin->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $admin->email }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $admin->company->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $admin->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center">
                            <form method="POST" action="{{ route('admins.destroy', $admin) }}"
                                  onsubmit="return confirm('Remove admin {{ $admin->name }}?')">
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
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No admins yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($admins->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $admins->links() }}</div>
    @endif
</div>

@endsection
