<x-app-layout :title="'Content Reports'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Content Reports
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php $tabs = ['pending' => 'Pending', 'resolved' => 'Resolved', 'dismissed' => 'Dismissed', 'all' => 'All']; @endphp
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.reports.index', ['status' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-sm font-medium {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($reports->isEmpty())
                <x-empty-state title="No reports found" description="There are no content reports matching this filter." />
            @else
                <div class="space-y-4">
                    @foreach ($reports as $report)
                        @php $type = class_basename($report->reportable_type ?? ''); @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Reported by {{ $report->reporter->name ?? 'Unknown user' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ $report->reason }}</p>
                                    @if ($report->details)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $report->details }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $report->created_at->diffForHumans() }}</p>
                                </div>
                                <x-status-badge :status="$report->status" />
                            </div>

                            <div class="mt-3 p-3 rounded-md bg-gray-50 dark:bg-gray-900/40 text-sm">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1">Reported {{ $type ?: 'item' }}</p>
                                @if (! $report->reportable)
                                    <p class="text-gray-500 dark:text-gray-400 italic">This item has been deleted.</p>
                                @elseif ($type === 'User')
                                    <p class="text-gray-700 dark:text-gray-300">{{ $report->reportable->name ?? '—' }} &middot; {{ $report->reportable->email ?? '' }}</p>
                                @elseif ($type === 'Review')
                                    <div class="flex items-center gap-2">
                                        <x-star-rating :rating="$report->reportable->rating ?? 0" size="w-3.5 h-3.5" />
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $report->reportable->comment ?? '—' }}</p>
                                @elseif ($type === 'Message')
                                    <p class="text-gray-700 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($report->reportable->body ?? '', 200) }}</p>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">No preview available.</p>
                                @endif
                            </div>

                            @if ($report->status === 'resolved' || $report->status === 'dismissed')
                                @if ($report->resolution_notes)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3"><span class="font-medium">Resolution notes:</span> {{ $report->resolution_notes }}</p>
                                @endif
                            @else
                                <div class="mt-4 flex flex-wrap items-end gap-3">
                                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="flex flex-wrap items-end gap-2">
                                        @csrf
                                        <div>
                                            <x-input-label for="resolution_notes_{{ $report->id }}" value="Resolution notes (optional)" />
                                            <textarea id="resolution_notes_{{ $report->id }}" name="resolution_notes" rows="1" maxlength="1000"
                                                      class="mt-1 block w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                                        </div>
                                        <x-primary-button type="submit">Resolve</x-primary-button>
                                    </form>
                                    <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST"
                                          onsubmit="return confirm('Dismiss this report without action?');">
                                        @csrf
                                        <x-secondary-button type="submit">Dismiss</x-secondary-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div>{{ $reports->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
