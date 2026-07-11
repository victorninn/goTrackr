<div class="relative" id="exportWrap">

    {{-- Trigger --}}
    <button type="button"
        class="export-btn flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
        Export PDF
    </button>

    {{-- Dropdown --}}
    <div class="export-menu hidden absolute right-0 top-full mt-1 z-50 bg-white rounded-xl shadow-lg border border-gray-100 w-52 py-1">

        @foreach([
            ['type' => 'this_week', 'label' => '📅 This Week'],
            ['type' => 'last_week', 'label' => '⏮ Last Week'],
            ['type' => 'last_two_weeks', 'label' => '📆 Last 2 Weeks'],
            ['type' => 'this_month', 'label' => '🗓 This Month'],
            ['type' => 'last_month', 'label' => '⏪ Last Month'],
        ] as $opt)
            <a href="{{ route($route, array_merge(request()->only(['employee_id']), ['type' => $opt['type']])) }}"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                {{ $opt['label'] }}
            </a>
        @endforeach

        <hr class="my-1 border-gray-100">

        <button type="button"
            class="custom-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">
            🗃 Custom Range…
        </button>
    </div>

    {{-- Modal --}}
    <div class="export-modal hidden fixed inset-0 z-50 items-center justify-center bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 w-full max-w-sm mx-4">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                Export Custom Date Range
            </h3>

            <form method="GET" action="{{ route($route) }}">
                <input type="hidden" name="type" value="custom">

                @if(request('employee_id'))
                    <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                @endif

                <div class="space-y-3 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                        <input type="date" name="date_from" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                        <input type="date" name="date_to" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm">
                        Export PDF
                    </button>

                    <button type="button"
                        class="close-modal flex-1 border border-gray-200 hover:bg-gray-50 text-gray-600 py-2 rounded-lg text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>