<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Mock Test Builder</h2>
        <p class="text-sm text-slate-500 mt-1">Create tests, attach assets, set section/duration, and publish.</p>
    </x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            @if (session()->has('message'))
                <div class="mb-4 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 px-4 py-2 text-sm">{{ session('message') }}</div>
            @endif

            <form wire:submit="createTest" class="space-y-4">
                <div>
                    <label class="text-sm font-medium">Test Title</label>
                    <input type="text" wire:model="title" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    @error('title') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Duration (minutes)</label>
                    <input type="number" wire:model="duration_minutes" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                    @error('duration_minutes') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Section Type</label>
                    <select wire:model="section_type" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="reading">Reading</option>
                        <option value="listening">Listening</option>
                        <option value="writing">Writing</option>
                        <option value="speaking">Speaking</option>
                    </select>
                    @error('section_type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Attach Assets</label>
                    <div class="mt-2 max-h-48 overflow-auto space-y-2 rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                        @foreach($availableAssets as $asset)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" value="{{ $asset->id }}" wire:model="selectedAssets" />
                                <span>{{ $asset->title }} <span class="text-xs text-slate-500">({{ $asset->type }})</span></span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedAssets') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Create Mock Test</button>
            </form>
        </div>

        <div class="xl:col-span-2 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Existing Tests</h3>
            <div class="space-y-3">
                @forelse($existingTests as $test)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h4 class="font-semibold">{{ $test->title }}</h4>
                                <p class="text-xs text-slate-500">{{ $test->duration_minutes }} min • Sections: {{ $test->sections->count() }} • Creator: {{ $test->creator->name ?? 'N/A' }}</p>
                                <p class="text-xs mt-1">
                                    @foreach($test->sections as $section)
                                        <span class="inline-block mr-2 px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-800">{{ $section->section_type }} ({{ $section->items->count() }})</span>
                                    @endforeach
                                </p>
                            </div>
                            <button wire:click="togglePublish({{ $test->id }})" class="rounded-lg px-3 py-2 text-sm {{ $test->is_published ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white' }}">
                                {{ $test->is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No mock tests found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
