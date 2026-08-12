<x-app-layout :title="'Users'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('admin.users.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2">
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" class="mt-1 block w-full" value="{{ request('q') }}" placeholder="Name or email" />
                </div>
                <div>
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All roles</option>
                        <option value="student" @selected(request('role') === 'student')>Student</option>
                        <option value="tutor" @selected(request('role') === 'tutor')>Tutor</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                        <option value="banned" @selected(request('status') === 'banned')>Banned</option>
                    </select>
                </div>
                <div class="sm:col-span-4 flex justify-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 self-center">Reset</a>
                    <x-primary-button type="submit">Filter</x-primary-button>
                </div>
            </form>

            @if ($users->isEmpty())
                <x-empty-state title="No users found" description="Try adjusting your filters." />
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Sessions</th>
                                    <th class="px-4 py-3">Joined</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $user->role }}</span>
                                        </td>
                                        <td class="px-4 py-3"><x-status-badge :status="$user->status" /></td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                            {{ $user->bookings_as_student_count }} as student · {{ $user->bookings_as_tutor_count }} as tutor
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $user->created_at->format('D, M j, Y') }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
