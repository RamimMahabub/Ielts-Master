<div>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">IELTS Vocabulary Builder</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">Study topic-based words with meaning, sentence, collocation, and a mini quiz.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/70">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <article class="rounded-2xl border border-sky-200/70 bg-sky-50 p-4 dark:border-sky-800 dark:bg-sky-900/20">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700 dark:text-sky-300">Total Words</p>
                    <p class="mt-2 text-3xl font-bold text-sky-700 dark:text-sky-200">{{ $totalWords }}</p>
                </article>
                <article class="rounded-2xl border border-emerald-200/70 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700 dark:text-emerald-300">Mastered</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700 dark:text-emerald-200">{{ $masteredWords }}</p>
                </article>
                <article class="rounded-2xl border border-amber-200/70 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-amber-700 dark:text-amber-300">Learning</p>
                    <p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-200">{{ $learningWords }}</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/70">
                    <label class="block text-xs font-semibold uppercase tracking-[0.12em] text-slate-700 dark:text-slate-300">Words Per Day</label>
                    <select wire:model.live="dailyLimit" class="mt-2 w-full rounded-xl border-slate-300 bg-white text-sm dark:border-slate-600 dark:bg-slate-900">
                        <option value="10">10 words/day</option>
                        <option value="15">15 words/day</option>
                        <option value="20">20 words/day</option>
                    </select>
                </article>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Today's Vocabulary Batch</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Pick a topic, then mark each word as new, learning, or mastered.</p>
                </div>
                <div class="flex items-center gap-2">
                    <select wire:model.live="selectedTopicId" class="rounded-xl border-slate-300 bg-white text-sm dark:border-slate-600 dark:bg-slate-900">
                        @forelse($topics as $topic)
                            <option value="{{ $topic['id'] }}">{{ $topic['name'] }}</option>
                        @empty
                            <option value="">No topics yet</option>
                        @endforelse
                    </select>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ count($todaysWords) }} words</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @forelse($todaysWords as $item)
                    @php($status = $wordStatusMap[$item['id']] ?? 'new')
                    <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800/60">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $item['word'] }}</h4>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $status === 'mastered' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($status === 'learning' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200') }}">{{ ucfirst($status) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-200"><span class="font-semibold">Meaning:</span> {{ $item['meaning'] }}</p>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300"><span class="font-semibold">Example:</span> {{ $item['example_sentence'] }}</p>
                        <p class="mt-1 text-sm text-sky-700 dark:text-sky-300"><span class="font-semibold">Collocation:</span> {{ $item['collocations'][0]['collocation'] ?? 'N/A' }}</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <button wire:click="setWordStatus({{ $item['id'] }}, 'new')" type="button" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">New</button>
                            <button wire:click="setWordStatus({{ $item['id'] }}, 'learning')" type="button" class="rounded-lg border border-amber-300 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20">Learning</button>
                            <button wire:click="setWordStatus({{ $item['id'] }}, 'mastered')" type="button" class="rounded-lg border border-emerald-300 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-700 dark:text-emerald-300 dark:hover:bg-emerald-900/20">Mastered</button>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Vocabulary list has not been seeded yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Mini Quiz (5 Questions)</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Practice topic collocations and usage to improve IELTS band score.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="startSectionQuiz" type="button" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Start Quiz</button>
                </div>
            </div>

            @if($quizStarted && !$quizFinished && $currentQuestion)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">Question {{ $quizIndex + 1 }} of {{ count($quizQuestions) }}</p>
                    <p class="mt-2 text-base font-semibold text-slate-900 dark:text-white">{{ $currentQuestion['question'] }}</p>
                    <div class="mt-4 grid grid-cols-1 gap-2 md:grid-cols-2">
                        @foreach($currentQuestion['options_json'] as $option)
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900">
                                <input type="radio" wire:model.live="selectedOption" value="{{ $option }}">
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button wire:click="submitAnswer" type="button" class="mt-4 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" @disabled(!$selectedOption)>Submit Answer</button>
                </div>
            @elseif($quizFinished)
                <div class="rounded-2xl border border-emerald-300 bg-emerald-50 p-4 dark:border-emerald-700 dark:bg-emerald-900/20">
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">Quiz complete.</p>
                    <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-300">Score: {{ $quizScore }} / {{ count($quizQuestions) }}</p>
                </div>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Start a quiz for the selected topic.</p>
            @endif
        </section>
    </div>
</div>
