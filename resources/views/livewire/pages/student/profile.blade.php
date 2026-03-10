<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Student Profile</h2>
        <p class="text-sm text-slate-500 mt-1">Manage your target band, preferred test date, and profile photo.</p>
    </x-slot>

    <div class="max-w-3xl rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
        @if (session()->has('message'))
            <div class="mb-4 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 px-4 py-2 text-sm">{{ session('message') }}</div>
        @endif

        <form wire:submit="updateProfile" class="space-y-4">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex items-center justify-center">
                    @if($currentPhoto)
                        <img src="{{ Storage::url($currentPhoto) }}" alt="Profile" class="h-full w-full object-cover">
                    @else
                        <span class="text-xs text-slate-500">No Photo</span>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium">Profile Photo</label>
                    <input type="file" wire:model="profile_photo" class="mt-1 w-full text-sm" />
                    @error('profile_photo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" wire:model="name" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Target Band</label>
                <input type="text" wire:model="target_band" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" placeholder="e.g. 7.5" />
                @error('target_band') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Preferred Test Date</label>
                <input type="date" wire:model="preferred_test_date" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                @error('preferred_test_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition">Save Profile</button>
        </form>
    </div>
</div>
