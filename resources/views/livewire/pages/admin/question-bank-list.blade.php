<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Question Bank Management</h2>
        <p class="text-sm text-slate-500 mt-1">Create, update, and delete original IELTS-style questions.</p>
    </x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            @if (session()->has('message'))
                <div class="mb-4 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 px-4 py-2 text-sm">{{ session('message') }}</div>
            @endif

            <h3 class="font-semibold mb-3">{{ $selectedAssetId ? 'Edit Asset & Question' : 'New Asset & Question' }}</h3>
            <form wire:submit="saveAsset" class="space-y-3">
                <input type="text" wire:model="title" placeholder="Asset title" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                <select wire:model="type" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    <option value="reading_passage">Reading</option>
                    <option value="listening_audio">Listening</option>
                    <option value="writing_task">Writing</option>
                    <option value="speaking_part">Speaking</option>
                </select>
                <textarea wire:model="body_text" rows="4" placeholder="Passage / task / transcript" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800"></textarea>
                <input type="text" wire:model="instructions" placeholder="Instructions" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                <input type="text" wire:model="question_type" placeholder="Question type (short_answer, essay...)" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                <input type="number" wire:model="q_no" min="1" placeholder="Question number" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                <textarea wire:model="prompt" rows="2" placeholder="Question prompt" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800"></textarea>
                <input type="text" wire:model="answer_text" placeholder="Correct answer (for auto-gradable questions)" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />

                <div class="flex gap-2">
                    <button type="submit" class="rounded-xl px-4 py-2 bg-indigo-600 text-white text-sm hover:bg-indigo-700">Save</button>
                    <button type="button" wire:click="resetForm" class="rounded-xl px-4 py-2 bg-slate-200 dark:bg-slate-700 text-sm">Reset</button>
                </div>
            </form>
        </div>

        <div class="xl:col-span-2 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            <div class="flex flex-col md:flex-row gap-3 mb-4">
                <input type="text" wire:model.live="search" placeholder="Search by title" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800" />
                <select wire:model.live="filterType" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    <option value="all">All types</option>
                    <option value="reading_passage">Reading</option>
                    <option value="listening_audio">Listening</option>
                    <option value="writing_task">Writing</option>
                    <option value="speaking_part">Speaking</option>
                </select>
            </div>

            <div class="space-y-3">
                @forelse($assets as $asset)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">{{ $asset->title }}</h3>
                                <p class="text-xs text-slate-500">Type: {{ $asset->type }} • Groups: {{ $asset->questionGroups->count() }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="editAsset({{ $asset->id }})" class="rounded-lg px-3 py-1 text-xs bg-indigo-600 text-white">Edit</button>
                                <button wire:click="deleteAsset({{ $asset->id }})" class="rounded-lg px-3 py-1 text-xs bg-rose-600 text-white">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No assets found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
