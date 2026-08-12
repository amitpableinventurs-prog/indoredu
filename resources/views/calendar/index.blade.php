<x-app-layout :title="'My Calendar'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Calendar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color:#f59e0b"></span> Pending</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color:#4f46e5"></span> Confirmed</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color:#16a34a"></span> Completed</span>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div id="calendar"></div>
            </div>

        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
                height: 'auto',
                events: '{{ route('calendar.events') }}',
            });
            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>
