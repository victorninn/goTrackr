@extends('layouts.app')

@section('page-title', 'Create Admin')
@section('page-subtitle', 'Add a new admin user')

@section('content')

<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admins.store') }}" class="space-y-5">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

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

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    Create Admin
                </button>
                <a href="{{ route('admins.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5">Cancel</a>
            </div>

        </form>
    </div>
</div>

@endsection
