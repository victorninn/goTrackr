@extends('layouts.app')

@section('page-title', 'Edit Time Log')
@section('page-subtitle', 'Update employee time record')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('logs.index') }}"
                class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-base font-semibold text-gray-800">Edit Time Log</h2>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg">
            <ul class="text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('logs.update', $log) }}">
            @csrf
            @method('PUT')

            {{-- Employee --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">Employee <span class="text-red-400">*</span></label>
                <select name="user_id" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('user_id', $log->user_id) == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">Date <span class="text-red-400">*</span></label>
                <input type="date" name="date" required
                    value="{{ old('date', $log->date->format('Y-m-d')) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Clock In & Clock Out --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Clock In <span class="text-red-400">*</span></label>
                    <input type="time" name="clock_in" required
                        value="{{ old('clock_in', \Carbon\Carbon::parse($log->clock_in)->format('H:i')) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Clock Out <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="time" name="clock_out"
                        value="{{ old('clock_out', $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('H:i') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label class="block text-xs font-medium text-gray-500 mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea name="description" rows="3"
                    placeholder="What did the employee work on?"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description', $log->description) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Update Log
                </button>
                <a href="{{ route('logs.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 transition-colors">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@endsection