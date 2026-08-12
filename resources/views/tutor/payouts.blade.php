<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Earnings & Payouts
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-stat-tile label="Total earned" :value="'₹'.number_format($earned, 2)" />
                <x-stat-tile label="Available balance" :value="'₹'.number_format($available, 2)" />
                <x-stat-tile label="Paid out" :value="'₹'.number_format($paidOut, 2)" />
            </div>

            <x-card title="Request a payout">
                <form action="{{ route('tutor.payouts.store') }}" method="POST" class="grid sm:grid-cols-3 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="amount" value="Amount (₹)" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="1" max="{{ $available }}" class="mt-1 block w-full" value="{{ old('amount') }}" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="method" value="Payout method" />
                        <select id="method" name="method" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="bank_transfer" @selected(old('method') === 'bank_transfer')>Bank transfer</option>
                            <option value="paypal" @selected(old('method') === 'paypal')>PayPal</option>
                        </select>
                        <x-input-error :messages="$errors->get('method')" class="mt-1" />
                    </div>
                    <div>
                        <x-primary-button type="submit" class="w-full justify-center" :disabled="$available <= 0">Request payout</x-primary-button>
                    </div>
                </form>
                @if ($available <= 0)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">You have no available balance to withdraw right now.</p>
                @endif
            </x-card>

            <x-card title="Payout history">
                @if ($payouts->isEmpty())
                    <x-empty-state title="No payouts yet" description="Payouts you request will show up here." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 pr-4">Requested</th>
                                    <th class="py-2 pr-4">Amount</th>
                                    <th class="py-2 pr-4">Method</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Processed</th>
                                    <th class="py-2 pr-4">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($payouts as $payout)
                                    <tr>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $payout->requested_at?->format('D, M j, Y') }}</td>
                                        <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">₹{{ number_format($payout->amount, 2) }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $payout->method === 'bank_transfer' ? 'Bank transfer' : 'PayPal' }}</td>
                                        <td class="py-3 pr-4"><x-status-badge :status="$payout->status" /></td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $payout->processed_at?->format('D, M j, Y') ?? '—' }}</td>
                                        <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">{{ $payout->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $payouts->links() }}</div>
                @endif
            </x-card>

        </div>
    </div>
</x-app-layout>
