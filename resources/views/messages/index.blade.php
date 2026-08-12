<x-app-layout :title="'Messages'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Messages
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                @forelse ($conversations as $conversation)
                    @php
                        $others = $conversation->participants->reject(fn ($p) => $p->id === auth()->id());
                        $otherNames = $others->pluck('name')->join(', ') ?: 'Conversation';
                        $myPivot = $conversation->participants->firstWhere('id', auth()->id())?->pivot;
                        $lastReadAt = $myPivot?->last_read_at ? \Illuminate\Support\Carbon::parse($myPivot->last_read_at) : null;
                        $isUnread = $conversation->last_message_at
                            && (! $lastReadAt || $lastReadAt->lt($conversation->last_message_at))
                            && $conversation->latestMessage
                            && $conversation->latestMessage->sender_id !== auth()->id();
                    @endphp
                    <a href="{{ route('messages.show', $conversation) }}"
                       class="flex items-center gap-4 p-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-sm shrink-0">
                            {{ $others->first()?->initials() ?? '?' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900 dark:text-gray-100 truncate {{ $isUnread ? 'font-bold' : '' }}">
                                    {{ $otherNames }}
                                </p>
                                @if ($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400 shrink-0"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate {{ $isUnread ? 'text-gray-700 dark:text-gray-300 font-medium' : '' }}">
                                @if ($conversation->latestMessage)
                                    @if ($conversation->latestMessage->sender_id === auth()->id())
                                        <span class="text-gray-400 dark:text-gray-500">You:</span>
                                    @endif
                                    {{ \Illuminate\Support\Str::limit($conversation->latestMessage->body, 60) }}
                                @else
                                    No messages yet.
                                @endif
                            </p>
                        </div>
                        <div class="shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $conversation->last_message_at?->diffForHumans() }}
                        </div>
                    </a>
                @empty
                    <x-empty-state title="No conversations yet" description="Messages with tutors and students will show up here." />
                @endforelse
            </div>

            @if ($conversations->hasPages())
                <div class="mt-6">{{ $conversations->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
