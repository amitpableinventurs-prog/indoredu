<x-app-layout :title="'Payment History'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Payment History
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($payments->isEmpty())
                    <x-empty-state title="No payments yet" description="Your payment history will show up here." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/40 text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 font-medium">Date</th>
                                    <th class="px-4 sm:px-6 py-3 font-medium">Description</th>
                                    <th class="px-4 sm:px-6 py-3 font-medium text-right">Amount</th>
                                    <th class="px-4 sm:px-6 py-3 font-medium">Status</th>
                                    <th class="px-4 sm:px-6 py-3 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($payments as $payment)
                                    @php
                                        if ($payment->booking) {
                                            $description = 'Session — '.($payment->booking->subject->name ?? 'Session').' with '.($payment->booking->tutor->name ?? '');
                                        } elseif ($payment->courseEnrollment) {
                                            $description = 'Course — '.($payment->courseEnrollment->course->title ?? 'Course');
                                        } else {
                                            $description = 'Payment';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                            {{ ($payment->paid_at ?? $payment->created_at)->format('D, M j, Y') }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-gray-900 dark:text-gray-100">{{ $description }}</td>
                                        <td class="px-4 sm:px-6 py-3 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3"><x-status-badge :status="$payment->status" /></td>
                                        <td class="px-4 sm:px-6 py-3 text-right whitespace-nowrap">
                                            @if ($payment->status === 'completed')
                                                <a href="{{ route('payments.receipt', $payment) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Receipt</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($payments->hasPages())
                <div class="mt-6">{{ $payments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
