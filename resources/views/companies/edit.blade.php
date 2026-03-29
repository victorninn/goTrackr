@extends('layouts.app')

@section('page-title', 'Edit Company')
@section('page-subtitle', $company->name)

@section('content')

<div class="max-w-lg">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Current Logo --}}
        @if($company->logo)
        <div class="mb-5 flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
            <img src="{{ Storage::url($company->logo) }}" alt="Current Logo"
                class="w-16 h-16 rounded-lg object-cover border border-gray-200">
            <div>
                <p class="text-sm font-medium text-gray-700">Current Logo</p>
                <p class="text-xs text-gray-400">Upload a new image to replace</p>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('companies.update', $company) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Name</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    {{ $company->logo ? 'Replace Logo' : 'Company Logo' }}
                    <span class="text-gray-400 font-normal">(optional, max 2MB)</span>
                </label>
                <input type="file" name="logo" accept="image/*"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    Save Changes
                </button>
                <a href="{{ route('companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5">Cancel</a>
            </div>

        </form>
    </div>
</div>

@endsection
