<x-app-layout :title="'Payouts'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Payouts
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('admin.payouts.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'processing', 'paid', 'failed'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3 flex justify-end gap-2">
                    <a href="{{ route('admin.payouts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 self-center">Reset</a>
                    <x-primary-button type="submit">Filter</x-primary-button>
                </div>
            </form>

            @if ($payouts->isEmpty())
                <x-empty-state title="No payouts found" description="Try adjusting your filters." />
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-3">Tutor</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Method</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Requested</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($payouts as $payout)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $payout->tutorProfile->user->name ?? 'Unknown tutor' }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ strtoupper($payout->currency ?? 'INR') }} {{ number_format($payout->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 capitalize whitespace-nowrap">{{ str_replace('_', ' ', $payout->method) }}</td>
                                        <td class="px-4 py-3"><x-status-badge :status="$payout->status" /></td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $payout->requested_at?->format('D, M j, Y') ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            @if (in_array($payout->status, ['pending', 'processing']))
                                                <div class="flex items-center justify-end gap-3">
                                                    <form action="{{ route('admin.payouts.mark-paid', $payout) }}" method="POST"
                                                          onsubmit="return confirm('Mark this payout as paid?');">
                                                        @csrf
                                                        <button type="submit" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Mark paid</button>
                                                    </form>
                                                    <details class="inline-block">
                                                        <summary class="list-none cursor-pointer select-none text-sm font-medium text-red-600 dark:text-red-400 hover:underline">Reject</summary>
                                                        <form action="{{ route('admin.payouts.reject', $payout) }}" method="POST" class="mt-2 space-y-2 text-left w-56">
                                                            @csrf
                                                            <textarea name="notes" rows="2" maxlength="500"
                                                                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                                      placeholder="Reason (optional)"></textarea>
                                                            <x-danger-button type="submit">Confirm reject</x-danger-button>
                                                        </form>
                                                    </details>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $payouts->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
