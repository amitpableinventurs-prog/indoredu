<x-app-layout :title="'Payments'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Payments
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('admin.payments.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'processing', 'completed', 'failed', 'refunded', 'partially_refunded'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="gateway" value="Gateway" />
                    <select id="gateway" name="gateway" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All gateways</option>
                        @foreach (['stripe', 'paypal', 'razorpay'] as $g)
                            <option value="{{ $g }}" @selected(request('gateway') === $g)>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 flex justify-end gap-2">
                    <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 self-center">Reset</a>
                    <x-primary-button type="submit">Filter</x-primary-button>
                </div>
            </form>

            @if ($payments->isEmpty())
                <x-empty-state title="No payments found" description="Try adjusting your filters." />
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-3">Payer &rarr; Payee</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Platform fee</th>
                                    <th class="px-4 py-3">Gateway</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Paid</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($payments as $payment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            {{ $payment->payer->name ?? '—' }} <span class="text-gray-400">&rarr;</span> {{ $payment->payee->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ strtoupper($payment->currency) }} {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ number_format($payment->platform_fee, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 capitalize">{{ $payment->gateway }}</td>
                                        <td class="px-4 py-3"><x-status-badge :status="$payment->status" /></td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $payment->paid_at?->format('D, M j, Y') ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            @if ($payment->status === 'completed')
                                                <form action="{{ route('admin.payments.refund', $payment) }}" method="POST"
                                                      onsubmit="return confirm('Refund this payment? This will reverse the transaction.');">
                                                    @csrf
                                                    <button type="submit" class="text-sm font-medium text-red-600 dark:text-red-400 hover:underline">Refund</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $payments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
