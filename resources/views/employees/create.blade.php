@extends('layouts.app')

@section('page-title', 'Add Employee')
@section('page-subtitle', 'Create a new employee account')

@section('content')

<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($errors->has('limit'))
        <div class="mb-5 bg-orange-50 border border-orange-300 rounded-lg px-4 py-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-orange-700">{{ $errors->first('limit') }}</p>
                <a href="{{ route('license.show') }}" class="text-sm text-orange-600 underline mt-0.5 inline-block">Upgrade your license →</a>
            </div>
        </div>
        @elseif($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('employees.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Company</label>
                <select name="company_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Hourly Rate ($)</label>
                <input type="number" name="hourly_rate" value="{{ old('hourly_rate', 0) }}"
                    step="0.01" min="0" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    Create Employee
                </button>
                <a href="{{ route('employees.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
