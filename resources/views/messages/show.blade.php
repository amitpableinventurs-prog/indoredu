@php
    $others = $conversation->participants->reject(fn ($p) => $p->id === auth()->id());
    $otherUser = $others->first();
    $otherNames = $others->pluck('name')->join(', ') ?: 'Conversation';
@endphp
<x-app-layout :title="$otherNames">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $otherNames }}
            </h2>
            <a href="{{ route('messages.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; All messages</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($otherUser)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Conversation with <span class="font-medium text-gray-900 dark:text-gray-100">{{ $otherUser->name }}</span></span>
                    <div class="flex gap-4">
                        <form action="{{ route('reports.store') }}" method="POST" onsubmit="return confirm('Report {{ $otherUser->name }} to our moderation team?');">
                            @csrf
                            <input type="hidden" name="reportable_type" value="user">
                            <input type="hidden" name="reportable_id" value="{{ $otherUser->id }}">
                            <input type="hidden" name="reason" value="Inappropriate messages">
                            <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Report</button>
                        </form>

                        @if (auth()->user()->hasBlocked($otherUser))
                            <form action="{{ route('users.unblock', $otherUser) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Unblock</button>
                            </form>
                        @else
                            <form action="{{ route('users.block', $otherUser) }}" method="POST" onsubmit="return confirm('Block {{ $otherUser->name }}? You will no longer be able to message or book with them.');">
                                @csrf
                                <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Block</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                    @forelse ($conversation->messages as $message)
                        @php $mine = $message->sender_id === auth()->id(); @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%]">
                                @if (! $mine)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5 ml-1">{{ $message->sender->name ?? 'Unknown' }}</p>
                                @endif
                                <div class="rounded-lg px-3 py-2 {{ $mine ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                    @if ($message->body)
                                        <p class="text-sm whitespace-pre-line break-words">{{ $message->body }}</p>
                                    @endif
                                    @if ($message->attachment_path)
                                        <a href="{{ route('files.message-attachment', $message) }}"
                                           class="inline-flex items-center gap-1.5 text-xs mt-1 {{ $mine ? 'text-indigo-100 hover:text-white' : 'text-indigo-600 dark:text-indigo-400 hover:underline' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.485 8.486L20.5 13"/></svg>
                                            {{ $message->attachment_name ?? 'Attachment' }}
                                        </a>
                                    @endif
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 {{ $mine ? 'text-right mr-1' : 'ml-1' }}">
                                    {{ $message->created_at->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <x-empty-state title="No messages yet" description="Say hello to start the conversation." />
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <form action="{{ route('messages.store', $conversation) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <textarea name="body" rows="3" placeholder="Write a message..."
                                  class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        <x-input-error :messages="$errors->get('body')" class="mt-1" />
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <input type="file" name="attachment"
                               class="text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600">
                        <x-primary-button type="submit">Send</x-primary-button>
                    </div>
                    <x-input-error :messages="$errors->get('attachment')" />
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const box = document.querySelector('textarea[name="body"]');
        setInterval(() => {
            if (document.visibilityState === 'visible' && box && box.value.trim() === '') {
                window.location.reload();
            }
        }, 15000);
    });
    </script>
    @endpush
</x-app-layout>
