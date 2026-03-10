<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Admin User Management</h2>
        <p class="text-sm text-slate-500 mt-1">Block/unblock users and assign instructor role.</p>
    </x-slot>

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
        <div class="mb-4 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <input type="text" wire:model.live="search" placeholder="Search users by name/email..." class="w-full md:w-96 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
            <a href="{{ route('admin.instructor.verification') }}" class="rounded-xl px-4 py-2 text-sm bg-indigo-600 text-white hover:bg-indigo-700 transition">Go to Instructor Approval</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 pr-3">Name</th>
                        <th class="py-3 pr-3">Email</th>
                        <th class="py-3 pr-3">Role</th>
                        <th class="py-3 pr-3">Instructor Status</th>
                        <th class="py-3 pr-3">Account</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <td class="py-3 pr-3">{{ $user->name }}</td>
                            <td class="py-3 pr-3">{{ $user->email }}</td>
                            <td class="py-3 pr-3">{{ $user->getRoleNames()->join(', ') ?: '-' }}</td>
                            <td class="py-3 pr-3 capitalize">{{ $user->instructor_status }}</td>
                            <td class="py-3 pr-3">
                                <span class="px-2 py-1 rounded-full text-xs {{ $user->is_blocked ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                    {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                                </span>
                            </td>
                            <td class="py-3 flex gap-2">
                                <button wire:click="toggleBlock({{ $user->id }})" class="rounded-lg px-3 py-1 text-xs {{ $user->is_blocked ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }} transition hover:opacity-90">
                                    {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                </button>
                                @if(!$user->hasRole('admin'))
                                    <button wire:click="assignInstructor({{ $user->id }})" class="rounded-lg px-3 py-1 text-xs bg-indigo-600 text-white transition hover:opacity-90">Make Instructor</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
