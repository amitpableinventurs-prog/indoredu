@php
    if ($payment->booking) {
        $description = 'Tutoring session — '.($payment->booking->subject->name ?? 'Session').' on '.$payment->booking->scheduled_date->format('D, M j, Y');
    } elseif ($payment->courseEnrollment) {
        $description = 'Course enrollment — '.($payment->courseEnrollment->course->title ?? 'Course');
    } else {
        $description = 'Payment';
    }
@endphp
<x-app-layout :title="'Receipt #'.$payment->id">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 print:hidden">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Receipt #{{ $payment->id }}
            </h2>
            <button onclick="window.print()" type="button" class="print:hidden inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                Print
            </button>
        </div>
    </x-slot>

    <div class="py-12 print:py-0">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8 print:shadow-none print:border-0">

                <div class="flex flex-wrap items-start justify-between gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">IndorEdu</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Receipt #{{ $payment->id }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Paid {{ $payment->paid_at?->format('D, M j, Y \a\t g:i A') ?? '—' }}</p>
                    </div>
                    <x-status-badge :status="$payment->status" />
                </div>

                <div class="grid sm:grid-cols-2 gap-6 py-6 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1">From</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $payment->payer->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->payer->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1">To</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $payment->payee->name ?? 'IndorEdu' }}</p>
                        @if ($payment->payee)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->payee->email }}</p>
                        @endif
                    </div>
                </div>

                <div class="py-6 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2">Description</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $description }}</p>
                </div>

                <div class="py-6">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr>
                                <td class="py-1.5 text-gray-500 dark:text-gray-400">Amount</td>
                                <td class="py-1.5 text-right font-medium text-gray-900 dark:text-gray-100">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 text-gray-500 dark:text-gray-400">Platform fee</td>
                                <td class="py-1.5 text-right font-medium text-gray-900 dark:text-gray-100">{{ $payment->currency }} {{ number_format($payment->platform_fee, 2) }}</td>
                            </tr>
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-1.5 pt-3 text-gray-500 dark:text-gray-400">Net to tutor</td>
                                <td class="py-1.5 pt-3 text-right font-semibold text-gray-900 dark:text-gray-100">{{ $payment->currency }} {{ number_format($payment->net_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap justify-between text-xs text-gray-400 dark:text-gray-500">
                    <span>Payment method: {{ ucfirst($payment->gateway) }}</span>
                    <span>Currency: {{ $payment->currency }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
