<x-app-layout :title="'Notifications'">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Notifications
            </h2>
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Mark all read</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $type = class_basename($notification->type);
                        $isRead = ! is_null($notification->read_at);

                        [$text, $link] = match ($type) {
                            'BookingStatusNotification' => [
                                'Booking '.($data['event'] ?? '').' for '.($data['scheduled_date'] ?? '').' at '.substr($data['start_time'] ?? '', 0, 5),
                                isset($data['booking_id']) ? route('bookings.show', $data['booking_id']) : null,
                            ],
                            'SessionReminderNotification' => [
                                'Upcoming session reminder for '.($data['scheduled_date'] ?? '').' at '.substr($data['start_time'] ?? '', 0, 5),
                                isset($data['booking_id']) ? route('bookings.show', $data['booking_id']) : null,
                            ],
                            'NewMessageNotification' => [
                                ($data['sender_name'] ?? 'Someone').': '.($data['preview'] ?? ''),
                                isset($data['conversation_id']) ? route('messages.show', $data['conversation_id']) : null,
                            ],
                            'EnquiryNotification' => [
                                match ($data['event'] ?? '') {
                                    'received' => ($data['student_name'] ?? 'A student').' sent you an enquiry: '.($data['title'] ?? ''),
                                    'replied' => ($data['tutor_name'] ?? 'The tutor').' replied to your enquiry: '.($data['title'] ?? ''),
                                    'declined' => ($data['tutor_name'] ?? 'The tutor').' declined your enquiry: '.($data['title'] ?? ''),
                                    default => 'Your enquiry was updated.',
                                },
                                isset($data['enquiry_id']) ? route('enquiries.show', $data['enquiry_id']) : null,
                            ],
                            'NewReviewNotification' => [
                                ($data['student_name'] ?? 'A student').' left you a '.($data['rating'] ?? '?').'-star review',
                                route('tutor.students.index'),
                            ],
                            'TutorApplicationStatusNotification' => [
                                'Your tutor application status: '.($data['status'] ?? ''),
                                route('tutor.profile.edit'),
                            ],
                            default => ['You have a new notification.', null],
                        };
                    @endphp
                    <div class="flex items-start gap-3 p-4 sm:px-6 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }} {{ $isRead ? 'opacity-60' : '' }}">
                        <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $isRead ? 'bg-transparent' : 'bg-indigo-600 dark:bg-indigo-400' }}"></span>
                        <div class="min-w-0 flex-1">
                            @if ($link)
                                <a href="{{ $link }}" class="text-sm {{ $isRead ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-gray-100 font-medium' }} hover:underline">
                                    {{ $text }}
                                </a>
                            @else
                                <p class="text-sm {{ $isRead ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-gray-100 font-medium' }}">{{ $text }}</p>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @unless ($isRead)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline whitespace-nowrap">Mark read</button>
                            </form>
                        @endunless
                    </div>
                @empty
                    <x-empty-state title="No notifications" description="You're all caught up." />
                @endforelse
            </div>

            @if ($notifications->hasPages())
                <div class="mt-6">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
