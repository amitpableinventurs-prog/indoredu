@php
    $options = [
        'email_bookings' => ['label' => 'Booking updates', 'description' => 'Confirmations, cancellations, and status changes for your sessions.'],
        'email_messages' => ['label' => 'Messages', 'description' => 'When someone sends you a message.'],
        'email_reminders' => ['label' => 'Session reminders', 'description' => 'A reminder before your sessions start.'],
        'email_marketing' => ['label' => 'Marketing', 'description' => 'Occasional product updates and tips from IndorEdu.'],
    ];
@endphp
<x-app-layout :title="'Notification Settings'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Notification Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <form action="{{ route('settings.notifications.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach ($options as $field => $meta)
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $preferences->$field) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ $meta['label'] }}</span>
                                <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $meta['description'] }}</span>
                            </span>
                        </label>
                    @endforeach

                    <div class="pt-2">
                        <x-primary-button type="submit">Save preferences</x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
