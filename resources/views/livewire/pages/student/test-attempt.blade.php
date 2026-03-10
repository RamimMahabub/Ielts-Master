<div x-data="{ submitted: false }" x-transition>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Mock Test Attempt</h2>
        <p class="text-sm text-slate-500 mt-1">{{ $mockTest->title }}</p>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2 text-sm">
            <div>Time limit: <span class="font-semibold">{{ $mockTest->duration_minutes }} minutes</span></div>
            <div>
                @if($lastSavedAt)
                    <span class="text-emerald-600 dark:text-emerald-300">Autosaved at {{ $lastSavedAt }}</span>
                @else
                    <span class="text-slate-500">Autosave enabled</span>
                @endif
            </div>
        </div>

        @foreach($mockTest->sections as $section)
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="font-semibold text-lg mb-3">{{ ucfirst($section->section_type) }} Section</h3>

                @foreach($section->items as $item)
                    <div class="mb-6 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50">
                        <h4 class="font-medium mb-2">{{ $item->asset->title ?? 'Asset' }}</h4>
                        @if($item->asset->body_text)
                            <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $item->asset->body_text }}</p>
                        @endif

                        @if($item->asset->transcript_text)
                            <p class="mt-2 text-xs text-slate-500">Transcript: {{ $item->asset->transcript_text }}</p>
                        @endif

                        @foreach($item->asset->questionGroups as $group)
                            <div class="mt-4">
                                <p class="text-sm text-slate-600 dark:text-slate-300 mb-2">{{ $group->instructions }}</p>
                                @foreach($group->questions as $question)
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium mb-1">Q{{ $question->q_no }}. {{ $question->prompt }}</label>
                                        <textarea
                                            rows="2"
                                            wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800"
                                        >{{ $answers[$question->id] ?? '' }}</textarea>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach

        <button
            wire:click="submitTest"
            @click="submitted = true"
            class="rounded-xl bg-emerald-600 px-5 py-2 text-white hover:bg-emerald-700 transition active:scale-95"
        >
            Submit Test
        </button>

        <div x-show="submitted" x-transition class="text-sm text-indigo-600 dark:text-indigo-300">Submitting attempt...</div>
    </div>
</div>
