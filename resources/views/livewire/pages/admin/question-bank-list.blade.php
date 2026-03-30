<div class="h-full">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-indigo-600 to-cyan-500 bg-clip-text text-transparent">Question Bank Management</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Design and manage complex IELTS Reading and Listening structures.</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="resetForm" class="px-5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold shadow-sm hover:translate-y-[-2px] transition-all text-sm">New Passage</button>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-6">
        <!-- List Panel -->
        <div class="lg:col-span-3 space-y-6">
            <div class="rounded-2xl bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl border border-white/50 dark:border-slate-800 p-5 shadow-sm">
                <div class="space-y-4">
                    <input type="text" wire:model.live="search" placeholder="Search assets..." class="w-full rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 focus:ring-2 focus:ring-indigo-500 text-sm py-2.5 px-4" />
                    <select wire:model.live="filterType" class="w-full rounded-xl bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="all">All Modules</option>
                        <option value="reading_passage">Reading</option>
                        <option value="listening_audio">Listening</option>
                    </select>
                </div>
                <div class="mt-6 space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($assets as $asset)
                        <div wire:click="editAsset({{ $asset->id }})" class="cursor-pointer rounded-xl p-3 border {{ $selectedAssetId === $asset->id ? 'bg-indigo-50 dark:bg-indigo-950/30 border-indigo-200 dark:border-indigo-800' : 'bg-transparent border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40' }} transition-all">
                            <div class="text-sm font-bold truncate">{{ $asset->title }}</div>
                            <div class="text-[10px] uppercase font-bold text-slate-400 mt-1 flex items-center gap-2">
                                <span>{{ str_replace('_', ' ', $asset->type) }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>{{ $asset->questionGroups->count() }} Groups</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Editor Panel -->
        <div class="lg:col-span-9">
            <div class="rounded-2xl bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl border border-white/50 dark:border-slate-800 shadow-xl overflow-hidden min-h-[800px] flex flex-col">
                <!-- Toolbar -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white/40 dark:bg-slate-800/40">
                    <h2 class="font-bold text-lg">{{ $selectedAssetId ? 'Editing Asset' : 'New Asset' }}</h2>
                    <button wire:click="saveAsset" class="px-6 py-2 rounded-xl bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-500/20 hover:scale-[1.02] transition-all text-sm">Save Changes</button>
                </div>

                <div class="flex-1 flex overflow-hidden">
                    <!-- Left: Passage Editor -->
                    <div class="w-1/2 border-r border-slate-200 dark:border-slate-800 p-6 space-y-6 overflow-y-auto custom-scrollbar">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Asset Title</label>
                            <input type="text" wire:model="title" class="w-full rounded-xl bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 focus:ring-2 focus:ring-indigo-500 text-sm font-bold py-3 px-4" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Content (Reading Passage / Listening Transcript)</label>
                            <textarea wire:model="body_text" class="w-full h-[500px] rounded-2xl bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800 focus:ring-2 focus:ring-indigo-500 p-6 text-sm leading-relaxed custom-scrollbar resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Right: Question Group Builder -->
                    <div class="w-1/2 bg-slate-50/50 dark:bg-slate-950/20 p-6 overflow-y-auto custom-scrollbar space-y-8">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Question Groups</h3>
                            <button wire:click="addGroup" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:underline">+ Add Group</button>
                        </div>

                        <div class="space-y-6">
                            @foreach($groups as $gi => $group)
                                <div class="relative rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                                    <div class="absolute -top-3 left-4 flex items-center gap-2">
                                        <span class="bg-indigo-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg uppercase">Group {{ $gi + 1 }}</span>
                                        <button wire:click="removeGroup({{ $gi }})" class="bg-rose-500 text-white p-0.5 rounded-full hover:bg-rose-600">×</button>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Pattern</label>
                                                <select wire:model="groups.{{ $gi }}.question_type" class="w-full rounded-lg bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-[11px] py-1 font-bold">
                                                    <option value="short_answer">Completion</option>
                                                    <option value="multiple_choice">MCQ</option>
                                                    <option value="true_false_not_given">T/F/NG</option>
                                                    <option value="matching_information">Matching</option>
                                                    <option value="table_completion">Table</option>
                                                </select>
                                            </div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Questions: Q{{ $group['start_no'] }}-{{ $group['end_no'] }}</div>
                                        </div>
                                        <textarea wire:model="groups.{{ $gi }}.instructions" class="w-full rounded-lg bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-[11px] py-2 px-3 h-14 resize-none" placeholder="Instructions..."></textarea>
                                        <div class="space-y-3">
                                            @foreach($group['questions'] as $qi => $q)
                                                <div class="p-3 rounded-xl bg-slate-50/50 dark:bg-slate-950/20 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                                                    <div class="h-6 w-6 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-[10px] text-indigo-600">{{ $q['q_no'] }}</div>
                                                    <div class="flex-1 space-y-2">
                                                        <input type="text" wire:model="groups.{{ $gi }}.questions.{{ $qi }}.prompt" placeholder="Prompt..." class="w-full border-none bg-transparent p-0 focus:ring-0 text-[11px] font-bold text-slate-700 dark:text-slate-200" />
                                                        <input type="text" wire:model="groups.{{ $gi }}.questions.{{ $qi }}.answer" placeholder="Correct Answer..." class="w-full rounded border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800 text-[10px] py-0.5 px-2 font-bold text-indigo-600 uppercase tracking-wide" />
                                                    </div>
                                                    <button wire:click="removeQuestion({{ $gi }}, {{ $qi }})" class="text-rose-400 hover:text-rose-500">×</button>
                                                </div>
                                            @endforeach
                                            <button wire:click="addQuestion({{ $gi }})" class="w-full py-1.5 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 text-[9px] font-bold uppercase tracking-widest text-slate-400 hover:border-indigo-400 hover:text-indigo-500 transition-all">+ Add Question</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button wire:click="addGroup" class="w-full py-4 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-800 text-slate-400 font-bold hover:text-indigo-600 hover:border-indigo-400 transition-all">+ New Question Group</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</div>