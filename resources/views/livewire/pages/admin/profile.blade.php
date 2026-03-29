<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Owner Profile</h2>
        <p class="text-sm text-slate-500 mt-1">Manage owner account settings and monitor platform health at a glance.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Total Users</p>
                <p class="text-3xl font-bold mt-2">{{ $totalUsers }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Students</p>
                <p class="text-3xl font-bold mt-2">{{ $totalStudents }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Instructors</p>
                <p class="text-3xl font-bold mt-2">{{ $totalInstructors }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Published Tests</p>
                <p class="text-3xl font-bold mt-2">{{ $totalPublishedTests }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Pending Instructor Requests</p>
                <p class="text-3xl font-bold mt-2">{{ $pendingInstructorRequests }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Owner Identity</h3>

                @if (session()->has('message'))
                    <div class="rounded-xl bg-emerald-50 text-emerald-700 px-4 py-3 text-sm border border-emerald-200 mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="mb-5 flex items-center gap-4">
                    @if ($profile_photo)
                        <img src="{{ $profile_photo->temporaryUrl() }}" alt="Profile preview" class="h-16 w-16 rounded-full object-cover ring-2 ring-indigo-500/30" />
                    @elseif ($currentPhoto)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($currentPhoto) }}" alt="Profile photo" class="h-16 w-16 rounded-full object-cover ring-2 ring-indigo-500/30" />
                    @else
                        <div class="h-16 w-16 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 text-xs">No photo</div>
                    @endif
                    <p class="text-sm text-slate-500">This profile is shown in the admin header and represents the owner account.</p>
                </div>

                <form wire:submit="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" wire:model.defer="name" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" wire:model.defer="email" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Profile Photo</label>
                        <input type="file" wire:model="profile_photo" accept="image/*" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('profile_photo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded-xl px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 transition">Save Owner Profile</button>
                        @if ($currentPhoto)
                            <button type="button" wire:click="removePhoto" class="rounded-xl px-4 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 transition">Remove Photo</button>
                        @endif
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Security</h3>

                @if (session()->has('password_message'))
                    <div class="rounded-xl bg-blue-50 text-blue-700 px-4 py-3 text-sm border border-blue-200 mb-4">
                        {{ session('password_message') }}
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Current Password</label>
                        <input type="password" wire:model.defer="current_password" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('current_password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">New Password</label>
                        <input type="password" wire:model.defer="new_password" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('new_password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                        <input type="password" wire:model.defer="new_password_confirmation" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('new_password_confirmation') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="rounded-xl px-4 py-2 bg-slate-800 text-white hover:bg-slate-900 transition">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
