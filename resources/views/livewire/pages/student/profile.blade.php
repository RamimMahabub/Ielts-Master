<div>
    @php
        $inputClasses = 'mt-1 w-full rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm text-slate-900 shadow-sm transition-all placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-100';
        $selectClasses = $inputClasses . ' appearance-none';
        $textareaClasses = $inputClasses . ' min-h-[120px]';
        $passwordInputClasses = 'w-full rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 pr-11 text-sm text-slate-900 shadow-sm transition-all placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-100';
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Student Profile</h2>
        <p class="text-sm text-slate-500 mt-1">Keep your study profile updated to receive better recommendations and track progress.</p>
    </x-slot>

    <div class="space-y-6 max-w-5xl">
        @if (session()->has('message'))
            <div class="rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 px-4 py-2 text-sm">{{ session('message') }}</div>
        @endif

        @if (session()->has('password_message'))
            <div class="rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 px-4 py-2 text-sm">{{ session('password_message') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 lg:col-span-2">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Profile Completion</p>
                        <p class="text-2xl font-bold mt-1">{{ $profileCompletion }}%</p>
                    </div>
                    <div class="w-40">
                        <div class="h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-cyan-500 to-indigo-600" style="width: {{ $profileCompletion }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Test Countdown</p>
                @if($daysUntilTest === null)
                    <p class="text-sm mt-2 text-slate-600 dark:text-slate-300">No preferred test date set.</p>
                @elseif($daysUntilTest < 0)
                    <p class="text-sm mt-2 text-amber-600">Your selected test date has passed.</p>
                @else
                    <p class="text-2xl font-bold mt-1">{{ $daysUntilTest }} days</p>
                @endif
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Average Score</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($averageScore, 1) }}</p>
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Target Band</p>
                <p class="text-2xl font-bold mt-1">{{ $target_band ?: '-' }}</p>
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Gap To Target</p>
                <p class="text-2xl font-bold mt-1">
                    @if($targetGap === null)
                        -
                    @else
                        {{ number_format($targetGap, 1) }}
                    @endif
                </p>
            </div>
        </div>

        <form wire:submit="updateProfile" class="space-y-6">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="text-lg font-semibold">Personal Info</h3>
                <p class="text-sm text-slate-500 mt-1">Manage your identity and contact details.</p>

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="h-20 w-20 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex items-center justify-center">
                        @if($currentPhoto)
                            <img src="{{ Storage::url($currentPhoto) }}" alt="Profile" class="h-full w-full object-cover">
                        @else
                            <span class="text-xs text-slate-500">No Photo</span>
                        @endif
                    </div>

                    <div class="flex-1">
                        <label class="block text-sm font-medium">Profile Photo</label>
                        <input type="file" wire:model="profile_photo" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/85 px-4 py-3 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-200 dark:file:bg-slate-700 dark:file:text-slate-100" />
                        @error('profile_photo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="profile_photo" class="text-xs text-slate-500 mt-1">Uploading...</div>
                    </div>

                    @if($currentPhoto)
                        <button type="button" wire:click="removePhoto" class="rounded-xl px-3 py-2 text-sm bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-900/40 dark:text-rose-300">Remove</button>
                    @endif
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Full Name</label>
                        <input type="text" wire:model="name" class="{{ $inputClasses }}" />
                        @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input type="text" wire:model="phone" class="{{ $inputClasses }}" placeholder="Optional" />
                        @error('phone') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Country</label>
                        <input type="text" wire:model="country" class="{{ $inputClasses }}" placeholder="Optional" />
                        @error('country') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Timezone</label>
                        <select wire:model="timezone" class="{{ $selectClasses }}">
                            @foreach($this->timezones as $zone)
                                <option value="{{ $zone }}">{{ $zone }}</option>
                            @endforeach
                        </select>
                        @error('timezone') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium">Short Bio</label>
                    <textarea wire:model="bio" rows="3" class="{{ $textareaClasses }}" placeholder="Tell us about your IELTS journey"></textarea>
                    @error('bio') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="text-lg font-semibold">Study Preferences</h3>
                <p class="text-sm text-slate-500 mt-1">Set goals and preferences used for planning your practice.</p>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Target Band</label>
                        <input type="number" step="0.5" min="0" max="9" wire:model="target_band" class="{{ $inputClasses }}" placeholder="e.g. 7.5" />
                        @error('target_band') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Preferred Test Date</label>
                        <input type="date" wire:model="preferred_test_date" class="{{ $inputClasses }}" />
                        @error('preferred_test_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Exam Type</label>
                        <select wire:model="exam_type" class="{{ $selectClasses }}">
                            <option value="">Select exam type</option>
                            <option value="academic">Academic</option>
                            <option value="general">General Training</option>
                        </select>
                        @error('exam_type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Daily Study Minutes</label>
                        <input type="number" min="0" max="600" wire:model="daily_study_minutes" class="{{ $inputClasses }}" placeholder="e.g. 120" />
                        @error('daily_study_minutes') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium">Study Goal</label>
                    <textarea wire:model="study_goal" rows="3" class="{{ $textareaClasses }}" placeholder="Describe your goal (e.g. scholarship, immigration, university admission)"></textarea>
                    @error('study_goal') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="mt-5">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition disabled:opacity-50" wire:loading.attr="disabled" wire:target="updateProfile,profile_photo">
                        <span wire:loading.remove wire:target="updateProfile,profile_photo">Save Profile</span>
                        <span wire:loading wire:target="updateProfile,profile_photo">Saving...</span>
                    </button>
                </div>
            </div>
        </form>

        <form wire:submit="updatePassword" class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 space-y-4">
            <h3 class="text-lg font-semibold">Security</h3>
            <p class="text-sm text-slate-500">Change your password regularly to keep your account secure.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium">Current Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" wire:model="current_password" class="{{ $passwordInputClasses }}" />
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300" aria-label="Toggle current password visibility">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587a2 2 0 102.828 2.828" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.953 9.953 0 0112 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.03 10.03 0 01-4.35 5.272M6.228 6.228A10.028 10.028 0 002.036 11.68a1.012 1.012 0 000 .639C3.423 16.49 7.36 19.5 12 19.5c1.494 0 2.915-.312 4.2-.874" />
                            </svg>
                        </button>
                    </div>
                    @error('current_password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium">New Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" wire:model="new_password" class="{{ $passwordInputClasses }}" />
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300" aria-label="Toggle new password visibility">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587a2 2 0 102.828 2.828" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.953 9.953 0 0112 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.03 10.03 0 01-4.35 5.272M6.228 6.228A10.028 10.028 0 002.036 11.68a1.012 1.012 0 000 .639C3.423 16.49 7.36 19.5 12 19.5c1.494 0 2.915-.312 4.2-.874" />
                            </svg>
                        </button>
                    </div>
                    @error('new_password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium">Confirm Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" wire:model="new_password_confirmation" class="{{ $passwordInputClasses }}" />
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300" aria-label="Toggle confirm password visibility">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587a2 2 0 102.828 2.828" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.953 9.953 0 0112 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.03 10.03 0 01-4.35 5.272M6.228 6.228A10.028 10.028 0 002.036 11.68a1.012 1.012 0 000 .639C3.423 16.49 7.36 19.5 12 19.5c1.494 0 2.915-.312 4.2-.874" />
                            </svg>
                        </button>
                    </div>
                    @error('new_password_confirmation') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>
            

            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition disabled:opacity-50" wire:loading.attr="disabled" wire:target="updatePassword">
                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                <span wire:loading wire:target="updatePassword">Updating...</span>
            </button>
        </form>
    </div>
</div>
