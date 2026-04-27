<div>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-2xl leading-tight">
                    {{ $itemId ? 'Edit' : 'New' }} {{ ucfirst($module) }} Item
                </h2>
                <p class="text-sm text-slate-500 mt-1">Build a reusable IELTS {{ $module }} bank item — usable across mock tests.</p>
            </div>
            <a href="{{ route('admin.question_bank') }}" class="rounded-xl px-3 py-2 bg-slate-100 dark:bg-slate-800 text-sm">Back</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 px-4 py-2 text-emerald-700 dark:text-emerald-300 text-sm">{{ session('status') }}</div>
    @endif

    @error('audio') <div class="mb-3 text-rose-500 text-sm">{{ $message }}</div> @enderror
    @error('image') <div class="mb-3 text-rose-500 text-sm">{{ $message }}</div> @enderror

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Item meta --}}
        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input type="text" wire:model="title" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                @error('title') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
            </div>

            @if($module === 'listening')
                <div>
                    <label class="block text-sm font-medium mb-1">Audio file (mp3/wav/m4a/ogg)</label>
                    <input type="file" wire:model="audio" accept="audio/*" class="text-sm">
                    @if($audioPath)
                        <audio controls class="mt-2 w-full">
                            <source src="{{ Storage::url($audioPath) }}">
                        </audio>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Transcript (optional)</label>
                    <textarea wire:model="transcript" rows="6" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900"></textarea>
                </div>
            @endif

            @if($module === 'reading')
                <div>
                    <label class="block text-sm font-medium mb-1">Passage subtitle (optional)</label>
                    <input type="text" wire:model="passageSubtitle" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Passage (HTML allowed)</label>
                    <textarea wire:model="passageHtml" rows="14" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 font-serif"></textarea>
                </div>
            @endif

            @if($module === 'writing')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Task number</label>
                        <select wire:model="taskNumber" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                            <option value="1">Task 1</option>
                            <option value="2">Task 2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Minimum words</label>
                        <input type="number" wire:model="minWords" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Image / chart (optional)</label>
                        <input type="file" wire:model="image" accept="image/*" class="text-sm">
                    </div>
                </div>
                @if($imagePath)
                    <img src="{{ Storage::url($imagePath) }}" class="mt-2 max-h-72 rounded-xl">
                @endif
                <div>
                    <label class="block text-sm font-medium mb-1">Prompt (HTML allowed)</label>
                    <textarea wire:model="promptHtml" rows="8" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900"></textarea>
                </div>
            @endif

            @if($module === 'speaking')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Part number</label>
                        <select wire:model="partNumber" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                            <option value="1">Part 1 — Introduction</option>
                            <option value="2">Part 2 — Cue Card</option>
                            <option value="3">Part 3 — Discussion</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Prompt / Intro (HTML)</label>
                    <textarea wire:model="promptHtml" rows="5" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Cue card text (Part 2)</label>
                    <textarea wire:model="cueCard" rows="5" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900"></textarea>
                </div>
            @endif
        </div>

        {{-- Question groups --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Question Groups</h3>
                <button type="button" wire:click="addGroup" class="rounded-xl px-3 py-2 text-sm bg-indigo-600 text-white hover:bg-indigo-500">+ Add Group</button>
            </div>

            @foreach($groups as $gIdx => $g)
                <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">
                            <div>
                                <label class="block text-xs font-medium mb-1 text-slate-500">Question type</label>
                                <select wire:model="groups.{{ $gIdx }}.question_type" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                                    @foreach($questionTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 text-slate-500">Instructions</label>
                                <input type="text" wire:model="groups.{{ $gIdx }}.instructions"
                                       placeholder="e.g. Choose the correct letter A, B, C or D."
                                       class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                            </div>
                        </div>
                        <button type="button" wire:click="removeGroup({{ $gIdx }})"
                                class="rounded-lg px-2 py-1 text-xs bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">Remove group</button>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-slate-500">
                            Shared options (one per line) — used for matching headings, matching features, etc.
                        </label>
                        <textarea wire:model="groups.{{ $gIdx }}.shared_data" rows="3"
                                  placeholder="i. Heading one&#10;ii. Heading two"
                                  class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"></textarea>
                    </div>

                    <div class="space-y-3">
                        @foreach($g['questions'] ?? [] as $qIdx => $q)
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 space-y-2">
                                <div class="flex items-center gap-3">
                                    <input type="number" min="1" max="40" wire:model="groups.{{ $gIdx }}.questions.{{ $qIdx }}.q_number"
                                           class="w-20 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm" title="Question #">
                                    <input type="text" wire:model="groups.{{ $gIdx }}.questions.{{ $qIdx }}.prompt"
                                           placeholder="Question prompt / stem"
                                           class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm">
                                    <button type="button" wire:click="removeQuestion({{ $gIdx }}, {{ $qIdx }})"
                                            class="rounded-lg px-2 py-1 text-xs bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">x</button>
                                </div>

                                @if(!empty($q['options']))
                                    <div class="space-y-1 pl-4">
                                        @foreach($q['options'] as $oIdx => $opt)
                                            <div class="flex items-center gap-2">
                                                <input type="text" wire:model="groups.{{ $gIdx }}.questions.{{ $qIdx }}.options.{{ $oIdx }}.key"
                                                       class="w-16 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm" placeholder="Key">
                                                <input type="text" wire:model="groups.{{ $gIdx }}.questions.{{ $qIdx }}.options.{{ $oIdx }}.text"
                                                       class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm" placeholder="Option text">
                                                <button type="button" wire:click="removeOption({{ $gIdx }}, {{ $qIdx }}, {{ $oIdx }})"
                                                        class="text-xs text-slate-500 hover:text-rose-600">remove</button>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addOption({{ $gIdx }}, {{ $qIdx }})" class="text-xs text-indigo-600">+ option</button>
                                    </div>
                                @endif

                                <div>
                                    <input type="text" wire:model="groups.{{ $gIdx }}.questions.{{ $qIdx }}.answers"
                                           placeholder="Correct answer(s) — separate alternates with |  e.g.  water|H2O"
                                           class="w-full rounded-lg border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/30 text-sm">
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="addQuestion({{ $gIdx }})"
                                class="rounded-lg px-3 py-1 text-sm bg-slate-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">+ Add question</button>
                    </div>
                </div>
            @endforeach

            @if(empty($groups))
                <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-8 text-center text-slate-500 text-sm">
                    No question groups yet. Click <strong>+ Add Group</strong> to start.
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.question_bank') }}" class="rounded-xl px-4 py-2 bg-slate-100 dark:bg-slate-800 text-sm">Cancel</a>
            <button type="submit" class="rounded-xl px-5 py-2 bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">Save</button>
        </div>
    </form>
</div>
