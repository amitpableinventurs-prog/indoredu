<x-app-layout :title="'Review Moderation'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Review Moderation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php $tabs = ['flagged' => 'Flagged', 'hidden' => 'Hidden', 'all' => 'All']; @endphp
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.reviews.index', ['filter' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-sm font-medium {{ $filter === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($reviews->isEmpty())
                <x-empty-state title="No reviews found" description="There are no reviews matching this filter." />
            @else
                <div class="space-y-4">
                    @foreach ($reviews as $review)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $review->student->name ?? 'Unknown student' }} &rarr; {{ $review->tutor->name ?? 'Unknown tutor' }}
                                    </p>
                                    <div class="mt-1"><x-star-rating :rating="$review->rating" size="w-3.5 h-3.5" /></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($review->is_flagged)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">Flagged</span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $review->is_approved ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                        {{ $review->is_approved ? 'Visible' : 'Hidden' }}
                                    </span>
                                </div>
                            </div>

                            @if ($review->comment)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-3">{{ $review->comment }}</p>
                            @endif

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST">
                                    @csrf
                                    <x-secondary-button type="submit">{{ $review->is_approved ? 'Hide' : 'Restore' }}</x-secondary-button>
                                </form>
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                      onsubmit="return confirm('Permanently delete this review? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">Delete</x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div>{{ $reviews->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
