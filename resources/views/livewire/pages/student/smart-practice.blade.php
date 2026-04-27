<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Smart Practice</h2>
        <p class="text-sm text-slate-500 mt-1">Targeted questions based on your recent weak sections and incorrect answer patterns.</p>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Recommended Focus</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $weaknessReport['summary'] ?? 'Complete more mock tests to unlock recommendations.' }}</p>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                @forelse(($weaknessReport['weakest_modules'] ?? []) as $weakness)
                    <article class="rounded-xl border border-rose-100 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-950/20">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-rose-900 dark:text-rose-100">{{ $weakness['label'] }}</p>
                            <span class="text-xs font-bold text-rose-700 dark:text-rose-200">Band {{ number_format($weakness['score'], 1) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-rose-800 dark:text-rose-100">{{ $weakness['message'] }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-600">No weak section detected yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Targeted Question Set</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Review these question formats before your next full mock test.</p>
                </div>
                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">{{ $recommendations->count() }} questions</span>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @forelse($recommendations as $question)
                    <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-white px-2 py-1 text-xs font-semibold capitalize text-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $question->group->item->module ?? 'practice' }}</span>
                            <span class="rounded-md bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">{{ str_replace('_', ' ', $question->group->question_type ?? 'question') }}</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $question->prompt ?: ($question->group->item->title ?? 'Practice question') }}</p>
                        @if(!empty($question->options_json))
                            <ul class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                                @foreach($question->options_json as $option)
                                    <li><span class="font-semibold">{{ $option['key'] ?? '' }}.</span> {{ $option['text'] ?? '' }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if(!empty($question->correct_answers_json))
                            <p class="mt-3 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Answer: {{ implode(', ', $question->correct_answers_json) }}</p>
                        @endif
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-600">Complete at least one mock test to generate targeted practice.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
