@php
    if ($payment->booking) {
        $description = 'Tutoring session — '.($payment->booking->subject->name ?? 'Session').' with '.$payment->booking->tutor->name.' on '.$payment->booking->scheduled_date->format('D, M j, Y');
    } elseif ($payment->courseEnrollment) {
        $description = 'Course enrollment — '.($payment->courseEnrollment->course->title ?? 'Course');
    } else {
        $description = 'Payment';
    }
@endphp
<x-app-layout :title="'Checkout'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Checkout
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Order summary</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $description }}</p>

                <div class="mt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Amount due</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>

            @if (count($gateways) > 1)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Payment method</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($gateways as $gatewayName)
                            <a href="{{ route('payments.checkout', ['payment' => $payment, 'gateway' => $gatewayName]) }}"
                               class="text-center py-3 rounded-md border text-sm font-medium capitalize {{ $payment->gateway === $gatewayName ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-400 dark:border-indigo-500 text-indigo-700 dark:text-indigo-300' : 'border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-indigo-300 dark:hover:border-indigo-700' }}">
                                {{ $gatewayName }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="{{ route('payments.redirect', $payment) }}"
               class="block w-full text-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500">
                Pay now with {{ ucfirst($payment->gateway) }}
            </a>
            <p class="text-xs text-center text-gray-400 dark:text-gray-500">You will be redirected to {{ ucfirst($payment->gateway) }} to complete your payment securely.</p>

        </div>
    </div>
</x-app-layout>
