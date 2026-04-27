<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Bookmarked Questions</h2>
        <p class="text-sm text-slate-500 mt-1">Questions you saved as difficult for later revision.</p>
    </x-slot>

    <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
        @if(!$bookmarksReady)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100">
                Bookmark storage is not ready yet. Run the latest database migrations to enable saved questions.
            </div>
        @elseif($bookmarks->isEmpty())
            <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500 dark:border-slate-600 dark:bg-slate-800/40">No bookmarked questions yet. Use the bookmark button while attempting a test to save difficult questions.</p>
        @else
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @foreach($bookmarks as $bookmark)
                    @php($question = $bookmark->question)
                    <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md bg-white px-2 py-1 text-xs font-semibold capitalize text-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $bookmark->source_module ?? ($question->group->item->module ?? 'practice') }}</span>
                                <span class="rounded-md bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">{{ str_replace('_', ' ', $question->group->question_type ?? 'question') }}</span>
                            </div>
                            <button wire:click="removeBookmark({{ $question->id }})" class="shrink-0 rounded-lg bg-white px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/30">Remove</button>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $question->group->item->title ?? 'Question bank item' }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $question->prompt ?: 'No prompt text available.' }}</p>
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
                @endforeach
            </div>
        @endif
    </section>
</div>
