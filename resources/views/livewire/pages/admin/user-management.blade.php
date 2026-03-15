<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight text-slate-800 dark:text-slate-100">User Management</h2>
        <p class="text-sm text-slate-500 mt-1">Manage users, their status, and role assignments.</p>
    </x-slot>

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow-sm p-6 relative overflow-hidden">
        <!-- Decorative blob -->
        <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-indigo-50 dark:bg-indigo-900/20 blur-3xl -z-10"></div>
        
        <div class="mb-8 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
            <div class="relative w-full md:w-[32rem] group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-6 h-6 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users by name or email address..." class="w-full pl-12 pr-12 py-3.5 rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-base shadow-sm font-medium" />
                
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center gap-2">
                    <div wire:loading wire:target="search" class="text-indigo-500">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    @if(strlen($search) > 0)
                        <button type="button" wire:click="$set('search', '')" class="text-slate-400 hover:text-rose-500 focus:outline-none transition-colors" title="Clear Search">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.instructor.verification') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-sm hover:translate-y-[-1px] hover:shadow-md transition-all">
                <span>Instructor Approvals</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white/50 dark:bg-slate-800/50">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300">
                        @foreach([
                            'name' => 'Name',
                            'email' => 'Email',
                            'role' => 'Role',
                            'instructor_status' => 'Instructor Status',
                            'is_blocked' => 'Account Status'
                        ] as $field => $label)
                            <th class="py-3 px-4 font-semibold cursor-pointer group hover:bg-slate-100 dark:hover:bg-slate-700 transition select-none" wire:click="sortBy('{{ $field }}')">
                                <div class="flex items-center gap-1">
                                    {{ $label }}
                                    <span class="text-slate-400 group-hover:text-indigo-500 transition-colors">
                                        @if($sortField === $field)
                                            @if($sortDirection === 'asc')
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            @else
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            @endif
                                        @else
                                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                        @endif
                                    </span>
                                </div>
                            </th>
                        @endforeach
                        <th class="py-3 px-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition-colors group">
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 shadow-sm">
                                    {{ ucfirst($user->getRoleNames()->first() ?? '-') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($user->instructor_status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        Approved
                                    </span>
                                @elseif($user->instructor_status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50">
                                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                                        Pending
                                    </span>
                                @elseif($user->instructor_status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50">
                                        <div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div>
                                        Rejected
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic text-xs">None</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium {{ $user->is_blocked ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                    @if($user->is_blocked)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Blocked
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Active
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-4 flex items-center justify-end gap-2">
                                <button wire:click="editUser({{ $user->id }})" class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                @if(!$user->hasRole('admin'))
                                    <button wire:click="toggleBlock({{ $user->id }})" class="rounded-lg px-3 py-1.5 text-xs font-medium {{ $user->is_blocked ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900' }} transition-colors">
                                        {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                    </button>
                                    <button wire:click="confirmDelete({{ $user->id }})" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500 italic px-2">Protected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <p class="text-slate-500 dark:text-slate-400">No users found matching your search.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:click.self="$set('showEditModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg p-8 transform transition-all border border-slate-100 dark:border-slate-700">
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Edit User Profile
            </h3>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                    <input type="text" wire:model="editName" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700/50 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-base shadow-sm" />
                    @error('editName') <span class="text-sm text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input type="email" wire:model="editEmail" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700/50 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-base shadow-sm" />
                    @error('editEmail') <span class="text-sm text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                @if($editRole !== 'admin')
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">User Role</label>
                        <select wire:model="editRole" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700/50 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-base shadow-sm">
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('editRole') <span class="text-sm text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-700">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="editIsBlocked" class="sr-only peer">
                            <div class="w-11 h-6 bg-emerald-100 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rose-300 dark:peer-focus:ring-rose-800 rounded-full peer dark:bg-emerald-900/30 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-500"></div>
                        </label>
                        <span class="text-sm font-medium {{ $editIsBlocked ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $editIsBlocked ? 'Account is Suspended' : 'Account is Active' }}
                        </span>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800/50">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm text-orange-800 dark:text-orange-300 leading-relaxed">
                                This user has Administrative privileges. You can edit their personal details, but you cannot modify their System Role or Suspension status to prevent accidental lockouts.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mt-8 flex justify-end gap-3">
                <button wire:click="$set('showEditModal', false)" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 dark:text-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl transition-all">Cancel</button>
                <button wire:click="updateUser" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">Save Changes</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:click.self="$set('showDeleteModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6 transform transition-all text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Delete User?</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
            
            <div class="flex justify-center gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 dark:text-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl transition">Cancel</button>
                <button wire:click="deleteUser" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">Yes, Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
