<div>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-2xl leading-tight">{{ $mockTestId ? 'Edit' : 'New' }} Mock Test</h2>
                <p class="text-sm text-slate-500 mt-1">Compose 4 modules from the question bank.</p>
            </div>
            <a href="{{ route('admin.mock_test') }}" class="rounded-xl px-3 py-2 bg-slate-100 dark:bg-slate-800 text-sm">Back</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 px-4 py-2 text-emerald-700 dark:text-emerald-300 text-sm">{{ session('status') }}</div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" wire:model="title" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                    @error('title') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Test type</label>
                    <select wire:model="testType" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                        <option value="academic">Academic</option>
                        <option value="general">General Training</option>
                    </select>
                </div>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="isPublished" class="rounded">
                Published (visible to students)
            </label>
        </div>

        @php
            $moduleLabels = [
                'listening' => 'Listening (4 sections, ~30 min)',
                'reading'   => 'Reading (3 passages, 60 min)',
                'writing'   => 'Writing (Task 1 + Task 2, 60 min)',
                'speaking'  => 'Speaking (3 parts, ~14 min)',
            ];
        @endphp

        @foreach(['listening', 'reading', 'writing', 'speaking'] as $module)
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <h3 class="text-lg font-semibold">{{ $moduleLabels[$module] }}</h3>
                    <div class="flex items-center gap-2 text-sm">
                        <label>Duration (min):</label>
                        <input type="number" min="1" wire:model="modules.{{ $module }}.duration"
                               class="w-24 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900">
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach($modules[$module]['item_ids'] as $idx => $itemId)
                        @php($bankItem = $bankItems[$module]->firstWhere('id', $itemId))
                        <div class="flex items-center gap-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 px-3 py-2">
                            <span class="text-xs font-bold text-slate-400 w-6">{{ $idx + 1 }}.</span>
                            <span class="flex-1 text-sm">{{ $bankItem->title ?? '(missing item)' }}</span>
                            <button type="button" wire:click="moveItem('{{ $module }}', {{ $idx }}, -1)" class="text-xs px-2 py-1">▲</button>
                            <button type="button" wire:click="moveItem('{{ $module }}', {{ $idx }}, 1)" class="text-xs px-2 py-1">▼</button>
                            <button type="button" wire:click="removeItem('{{ $module }}', {{ $idx }})" class="text-xs px-2 py-1 text-rose-600">remove</button>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-slate-200/70 dark:border-slate-700/70">
                    <select id="add-{{ $module }}" class="flex-1 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"
                            x-data x-ref="sel">
                        <option value="">— select a {{ $module }} bank item to add —</option>
                        @foreach($bankItems[$module] as $bi)
                            <option value="{{ $bi->id }}">{{ $bi->title }}</option>
                        @endforeach
                    </select>
                    <button type="button"
                            onclick="(() => { const s=document.getElementById('add-{{ $module }}'); if(s.value){ Livewire.find('{{ $this->getId() }}').call('addItem','{{ $module }}', parseInt(s.value)); s.value=''; } })()"
                            class="rounded-xl px-3 py-2 text-sm bg-indigo-600 text-white hover:bg-indigo-500">Add</button>
                    <a href="{{ route('admin.question_bank.create', ['module' => $module]) }}" target="_blank"
                       class="rounded-xl px-3 py-2 text-sm bg-slate-100 dark:bg-slate-800">+ Bank</a>
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.mock_test') }}" class="rounded-xl px-4 py-2 bg-slate-100 dark:bg-slate-800 text-sm">Cancel</a>
            <button type="submit" class="rounded-xl px-5 py-2 bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">Save</button>
        </div>
    </form>
</div>
