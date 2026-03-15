@php
    $allQuestionIds = [];
    foreach ($mockTest->sections as $sectionData) {
        foreach ($sectionData->items as $itemData) {
            foreach ($itemData->asset->questionGroups as $groupData) {
                foreach ($groupData->questions as $questionData) {
                    $allQuestionIds[] = $questionData->id;
                }
            }
        }
    }
@endphp

<div
    x-data="testAttemptUI({
        initialSeconds: {{ (int) $timeLeft }},
        initialEndsAt: @js($endsAtTimestamp),
        initialStarted: @js((bool) ($attempt->started_at && $attempt->status === 'in_progress' && (int) $timeLeft > 0)),
        questionIds: @js($allQuestionIds),
        initialAnswers: @js($answers),
    })"
    x-transition
>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Mock Test Attempt</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $mockTest->title }}</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
        <div class="space-y-4 xl:col-span-9">
            <section class="rounded-3xl border border-slate-200 bg-gradient-to-br from-sky-50 via-white to-emerald-50 p-5 shadow-sm dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 md:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-2">
                        <p class="inline-flex w-fit items-center rounded-full border border-sky-200 bg-sky-100/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-sky-700 dark:border-sky-800 dark:bg-sky-900/40 dark:text-sky-300">
                            IELTS Computer Mode
                        </p>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Press Start Test to begin the official timer</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">The timer counts down in real time. When it reaches 00:00, your attempt is submitted automatically.</p>
                    </div>

                    <div class="flex flex-col items-start gap-3 md:items-end">
                        <div class="rounded-2xl border px-4 py-3 text-right"
                            :class="critical
                                ? 'border-rose-300 bg-rose-50 text-rose-700 dark:border-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                                : warning
                                    ? 'border-amber-300 bg-amber-50 text-amber-700 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                    : 'border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200'"
                        >
                            <p class="text-xs font-medium uppercase tracking-[0.1em]">Time Left</p>
                            <p class="mt-1 text-2xl font-bold tabular-nums" x-text="formattedTime"></p>
                        </div>

                        <button
                            x-show="!started"
                            @click="startTest"
                            class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700"
                        >
                            Start Test
                        </button>
                    </div>
                </div>
            </section>

            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-200 dark:bg-slate-900/70 dark:border-slate-700 shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2 text-sm">
                <div>
                    Time limit: <span class="font-semibold">{{ $mockTest->duration_minutes }} minutes</span>
                    <span class="mx-2 text-slate-300">|</span>
                    Questions: <span class="font-semibold">{{ count($allQuestionIds) }}</span>
                </div>
                <div>
                    @if($lastSavedAt)
                        <span class="text-emerald-600 dark:text-emerald-300">Autosaved at {{ $lastSavedAt }}</span>
                    @else
                        <span class="text-slate-500">Autosave enabled</span>
                    @endif
                </div>
            </div>

            @foreach($mockTest->sections as $section)
                <section class="rounded-2xl bg-white/80 dark:bg-slate-900/70 backdrop-blur border border-slate-200 dark:border-slate-700 shadow p-6" id="section-{{ $section->id }}">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ ucfirst($section->section_type) }} Section</h3>
                        <span class="rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            {{ $section->items->sum(fn($item) => $item->asset->questionGroups->sum(fn($group) => $group->questions->count())) }} questions
                        </span>
                    </div>

                    @foreach($section->items as $item)
                        <article class="mb-6 rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                            <div class="mb-3 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <h4 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ $item->asset->title ?? 'Asset' }}</h4>

                                <div class="flex items-center gap-2">
                                    @if($section->section_type === 'reading' && $item->asset->body_text)
                                        <button
                                            type="button"
                                            @click="toggleReadAloud(@js($item->asset->body_text), 'asset-{{ $item->asset->id }}')"
                                            class="rounded-lg border border-sky-300 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 dark:border-sky-700 dark:bg-sky-900/30 dark:text-sky-300"
                                        >
                                            <span x-show="readingKey !== 'asset-{{ $item->asset->id }}'">Read Aloud</span>
                                            <span x-show="readingKey === 'asset-{{ $item->asset->id }}'">Stop Reading</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($section->section_type === 'listening' && $item->asset->file_path)
                                @php
                                    $audioPath = $item->asset->file_path;
                                    $audioSrc = \Illuminate\Support\Str::startsWith($audioPath, ['http://', 'https://'])
                                        ? $audioPath
                                        : asset('storage/' . ltrim($audioPath, '/'));
                                @endphp
                                <div class="mb-3 rounded-xl border border-emerald-200 bg-emerald-50/60 p-3 dark:border-emerald-800 dark:bg-emerald-900/20">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.1em] text-emerald-700 dark:text-emerald-300">Listening Audio</p>
                                    <audio controls class="w-full" preload="metadata">
                                        <source src="{{ $audioSrc }}">
                                        Your browser does not support audio playback.
                                    </audio>
                                </div>
                            @endif

                            @if($item->asset->body_text)
                                <p class="whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $item->asset->body_text }}</p>
                            @endif

                            @if($item->asset->transcript_text)
                                <p class="mt-2 text-xs text-slate-500">Transcript: {{ $item->asset->transcript_text }}</p>
                            @endif

                            @foreach($item->asset->questionGroups as $group)
                                <div class="mt-4">
                                    <p class="mb-2 text-sm text-slate-600 dark:text-slate-300">{{ $group->instructions }}</p>
                                    @foreach($group->questions as $question)
                                        <div class="mb-4 rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/70"
                                             id="question-{{ $question->id }}"
                                             :class="isFlagged({{ $question->id }}) ? 'ring-2 ring-amber-300 dark:ring-amber-700' : ''"
                                        >
                                            <div class="mb-2 flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                                <label class="block text-sm font-medium text-slate-800 dark:text-slate-100">Q{{ $question->q_no }}. {{ $question->prompt }}</label>
                                                <button
                                                    type="button"
                                                    @click="toggleFlag({{ $question->id }})"
                                                    class="inline-flex w-fit items-center rounded-md border px-2.5 py-1 text-xs font-semibold transition"
                                                    :class="isFlagged({{ $question->id }})
                                                        ? 'border-amber-300 bg-amber-50 text-amber-700 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                                        : 'border-slate-300 bg-slate-50 text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300'"
                                                >
                                                    <span x-text="isFlagged({{ $question->id }}) ? 'Marked for Review' : 'Mark for Review'"></span>
                                                </button>
                                            </div>

                                            <textarea
                                                id="answer-input-{{ $question->id }}"
                                                rows="4"
                                                wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                                                @input="updateAnswer({{ $question->id }}, $event.target.value)"
                                                :disabled="!started || secondsLeft <= 0 || submitted"
                                                class="!w-full min-w-full max-w-none min-h-24 rounded-xl border-slate-300 px-3 py-2 text-sm leading-relaxed dark:border-slate-700 dark:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                                            >{{ $answers[$question->id] ?? '' }}</textarea>

                                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs">
                                                <p class="text-slate-500 dark:text-slate-400">
                                                    <span x-text="wordCount({{ $question->id }})"></span> words
                                                </p>

                                                <button
                                                    type="button"
                                                    @click="toggleMic({{ $question->id }})"
                                                    :disabled="!started || secondsLeft <= 0 || submitted || !speechSupported"
                                                    class="inline-flex items-center rounded-md border px-3 py-1.5 font-semibold transition disabled:cursor-not-allowed disabled:opacity-60"
                                                    :class="listeningQuestionId === {{ $question->id }}
                                                        ? 'border-rose-300 bg-rose-50 text-rose-700 dark:border-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                                                        : 'border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300'"
                                                >
                                                    <span x-show="listeningQuestionId !== {{ $question->id }}">Mic Answer</span>
                                                    <span x-show="listeningQuestionId === {{ $question->id }}">Stop Mic</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </article>
                    @endforeach
                </section>
            @endforeach

            <div class="flex flex-wrap items-center gap-3">
                <button
                    wire:click="submitTest"
                    @click="submitted = true"
                    :disabled="!started || secondsLeft <= 0 || submitted"
                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Submit Test
                </button>

                <div x-show="submitted" x-transition class="text-sm text-indigo-600 dark:text-indigo-300">Submitting attempt...</div>

                <div x-show="!speechSupported" class="text-xs text-amber-700 dark:text-amber-300">
                    Mic dictation is not supported in this browser.
                </div>
            </div>
        </div>

        <aside class="xl:col-span-3">
            <div class="sticky top-4 rounded-2xl border border-slate-200 bg-white/85 p-4 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/80">
                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Question Navigator</h4>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Inspired by computer-delivered IELTS review panels.</p>

                <div class="mt-3 grid grid-cols-6 gap-2 md:grid-cols-8 xl:grid-cols-5">
                    @foreach($allQuestionIds as $questionId)
                        <button
                            type="button"
                            @click="goToQuestion({{ $questionId }})"
                            class="rounded-md border px-2 py-1.5 text-xs font-semibold transition"
                            :class="paletteClass({{ $questionId }})"
                        >
                            {{ $loop->iteration }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-4 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                    <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded bg-emerald-400"></span> Answered</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded bg-amber-400"></span> Marked for review</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded border border-slate-400 bg-white dark:bg-slate-800"></span> Not answered</div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    function testAttemptUI({ initialSeconds, initialEndsAt, initialStarted, questionIds, initialAnswers }) {
        return {
            started: !!initialStarted,
            submitted: false,
            secondsLeft: Number(initialSeconds || 0),
            endsAtEpoch: Number(initialEndsAt || 0),
            timer: null,
            warning: false,
            critical: false,
            answered: { ...initialAnswers },
            flagged: {},
            speechSupported: !!(window.SpeechRecognition || window.webkitSpeechRecognition),
            recognition: null,
            listeningQuestionId: null,
            readingKey: null,

            get formattedTime() {
                const minutes = Math.floor(this.secondsLeft / 60);
                const seconds = this.secondsLeft % 60;
                return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },

            async startTest() {
                if (this.started) {
                    return;
                }

                const response = await this.$wire.startAttempt();
                this.started = !!response?.started;
                this.secondsLeft = Number(response?.secondsLeft ?? this.secondsLeft);
                this.endsAtEpoch = Number(response?.endsAtTimestamp ?? this.endsAtEpoch);
                this.startCountdown();
            },

            startCountdown() {
                if (this.timer) {
                    clearInterval(this.timer);
                }

                this.timer = setInterval(() => {
                    if (this.endsAtEpoch > 0) {
                        const nowEpoch = Math.floor(Date.now() / 1000);
                        this.secondsLeft = Math.max(this.endsAtEpoch - nowEpoch, 0);
                    }

                    if (this.secondsLeft > 0) {
                        this.warning = this.secondsLeft <= 600;
                        this.critical = this.secondsLeft <= 300;
                        return;
                    }

                    clearInterval(this.timer);
                    this.secondsLeft = 0;
                    this.submitted = true;
                    this.started = false;
                    this.$wire.submitTest();
                }, 1000);
            },

            updateAnswer(questionId, value) {
                this.answered[questionId] = value;
            },

            wordCount(questionId) {
                const text = (this.answered[questionId] || '').trim();
                return text ? text.split(/\s+/).length : 0;
            },

            toggleFlag(questionId) {
                this.flagged[questionId] = !this.flagged[questionId];
            },

            isFlagged(questionId) {
                return !!this.flagged[questionId];
            },

            paletteClass(questionId) {
                if (this.isFlagged(questionId)) {
                    return 'border-amber-300 bg-amber-100 text-amber-700 dark:border-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
                }
                if ((this.answered[questionId] || '').trim() !== '') {
                    return 'border-emerald-300 bg-emerald-100 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
                }
                return 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300';
            },

            goToQuestion(questionId) {
                const target = document.getElementById(`question-${questionId}`);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            },

            toggleReadAloud(text, key) {
                if (!window.speechSynthesis || !text) {
                    return;
                }

                if (this.readingKey === key) {
                    window.speechSynthesis.cancel();
                    this.readingKey = null;
                    return;
                }

                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.rate = 1;
                utterance.pitch = 1;
                utterance.onend = () => {
                    this.readingKey = null;
                };
                this.readingKey = key;
                window.speechSynthesis.speak(utterance);
            },

            toggleMic(questionId) {
                if (!this.speechSupported || !this.started) {
                    return;
                }

                if (this.listeningQuestionId === questionId && this.recognition) {
                    this.recognition.stop();
                    this.listeningQuestionId = null;
                    return;
                }

                if (this.recognition) {
                    this.recognition.stop();
                }

                const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                this.recognition = new Recognition();
                this.recognition.lang = 'en-US';
                this.recognition.interimResults = false;
                this.recognition.maxAlternatives = 1;
                this.listeningQuestionId = questionId;

                this.recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript || '';
                    const current = (this.answered[questionId] || '').trim();
                    const nextValue = [current, transcript.trim()].filter(Boolean).join(' ');
                    this.answered[questionId] = nextValue;

                    const input = document.getElementById(`answer-input-${questionId}`);
                    if (input) {
                        input.value = nextValue;
                    }

                    this.$wire.saveAnswer(questionId, nextValue);
                };

                this.recognition.onend = () => {
                    this.listeningQuestionId = null;
                };

                this.recognition.onerror = () => {
                    this.listeningQuestionId = null;
                };

                this.recognition.start();
            },

            init() {
                for (const id of questionIds) {
                    if (typeof this.answered[id] === 'undefined') {
                        this.answered[id] = '';
                    }
                }

                this.warning = this.secondsLeft <= 600;
                this.critical = this.secondsLeft <= 300;

                if (this.started && this.endsAtEpoch > 0) {
                    const nowEpoch = Math.floor(Date.now() / 1000);
                    this.secondsLeft = Math.max(this.endsAtEpoch - nowEpoch, 0);
                }

                if (this.started && this.secondsLeft > 0) {
                    this.startCountdown();
                }
            }
        };
    }
</script>
