@php
    $module = $this->currentModuleObj;
    // Build flat question list with running global numbering (1..N for the module)
    $flatQuestions = [];
    if ($module) {
        $running = 0;
        foreach ($module->items as $item) {
            foreach ($item->bankItem->groups as $group) {
                foreach ($group->questions as $q) {
                    $running++;
                    $flatQuestions[] = ['n' => $running, 'q' => $q, 'group' => $group, 'item' => $item->bankItem];
                }
            }
        }
    }
    $totalQs = count($flatQuestions);
@endphp

<div x-data="ieltsTimer(@js($endsAtTimestamp), {{ $attempt->id }})" x-init="init()"
     class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 -mx-4 sm:-mx-6 lg:-mx-8 -mt-6 mb-0">

    {{-- Top bar --}}
    <div class="sticky top-0 z-30 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-2 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold">{{ $mockTest->title }}</span>
            <span class="text-xs uppercase rounded-full px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                {{ ucfirst($currentModule) }}
            </span>
        </div>
        <div class="flex items-center gap-3">
            @if($currentModule === 'speaking')
                {{-- Speaking has its own internal flow & no auto-cutoff timer --}}
                <span class="text-xs text-slate-500">Recorded speaking exam</span>
            @elseif($attempt->module_started_at)
                <div class="font-mono text-lg" :class="timeLeft <= 60 ? 'text-rose-600 animate-pulse' : ''">
                    <span x-text="formatTime(timeLeft)"></span>
                </div>
                <button wire:click="finishModule"
                        wire:confirm="Submit and lock the {{ $currentModule }} module?"
                        class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-3 py-2">
                    Submit module
                </button>
            @else
                <button wire:click="startModule"
                        class="rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-3 py-2">
                    Start {{ ucfirst($currentModule) }} ({{ $module?->duration_minutes }} min)
                </button>
            @endif
            @if($lastSavedAt)
                <span class="text-xs text-slate-400 hidden md:inline">Saved {{ $lastSavedAt }}</span>
            @endif
        </div>
    </div>

    {{-- Body --}}
    @if($currentModule !== 'speaking' && !$attempt->module_started_at)
        <div class="max-w-2xl mx-auto p-8 text-center space-y-4">
            <h2 class="text-2xl font-bold">{{ ucfirst($currentModule) }} module</h2>
            <p class="text-slate-600 dark:text-slate-400">
                You will have <strong>{{ $module?->duration_minutes }} minutes</strong> for this module.
                Once you click Start, the timer begins and cannot be paused.
            </p>
            <p class="text-sm text-slate-500">{{ $totalQs }} questions in this module.</p>
        </div>
    @elseif(!$module || $totalQs === 0)
        <div class="max-w-2xl mx-auto p-8 text-center">
            <p class="text-slate-600 dark:text-slate-400">No content set up for this module. Click submit to continue.</p>
        </div>
    @else
        @if($currentModule === 'listening')
            {{-- Listening: top audio (file OR browser-TTS fallback) + scroll of all questions --}}
            <div class="px-4 py-4 space-y-6 max-w-5xl mx-auto">
                @foreach($module->items as $sIdx => $item)
                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-3"
                         x-data="ttsPlayer(@js($item->bankItem->transcript ?? ''), 'tts-{{ $item->bankItem->id }}')">
                        <p class="text-xs uppercase font-semibold text-emerald-700 dark:text-emerald-300 mb-2">
                            Section {{ $sIdx + 1 }} — {{ $item->bankItem->title }}
                        </p>

                        @if($item->bankItem->audio_path)
                            <audio controls class="w-full">
                                <source src="{{ Storage::url($item->bankItem->audio_path) }}">
                                Your browser does not support the audio element.
                            </audio>
                        @elseif($item->bankItem->transcript)
                            <div class="flex items-center gap-3 flex-wrap">
                                <button type="button" @click="play()" x-show="!speaking"
                                        class="rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2 inline-flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                    Play audio
                                </button>
                                <button type="button" @click="pause()" x-show="speaking && !paused"
                                        class="rounded-lg bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold px-4 py-2">
                                    Pause
                                </button>
                                <button type="button" @click="resume()" x-show="paused"
                                        class="rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2">
                                    Resume
                                </button>
                                <button type="button" @click="stop()" x-show="speaking || paused"
                                        class="rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold px-4 py-2">
                                    Stop
                                </button>

                                <label class="text-xs text-emerald-700 dark:text-emerald-300 inline-flex items-center gap-1">
                                    Speed
                                    <select x-model.number="rate" class="rounded-md border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-900 text-xs">
                                        <option value="0.85">0.85x</option>
                                        <option value="1">1.0x</option>
                                        <option value="1.15">1.15x</option>
                                    </select>
                                </label>

                                <span class="text-xs text-emerald-700 dark:text-emerald-300">
                                    Audio is generated from the transcript using your browser's speech engine.
                                </span>
                            </div>
                        @else
                            <p class="text-xs text-emerald-700 dark:text-emerald-300">No audio configured for this section.</p>
                        @endif
                    </div>
                @endforeach
                @include('livewire.pages.student._test_questions', ['flatQuestions' => $flatQuestions])
            </div>

        @elseif($currentModule === 'reading')
            {{-- Reading: split layout — passage left, questions right --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 h-[calc(100vh-3.5rem)]">
                <div class="overflow-y-auto p-6 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                    @foreach($module->items as $item)
                        <article class="prose dark:prose-invert max-w-none mb-8">
                            <h2>{{ $item->bankItem->title }}</h2>
                            @if($item->bankItem->passage_subtitle)
                                <p class="text-sm text-slate-500 italic">{{ $item->bankItem->passage_subtitle }}</p>
                            @endif
                            {!! $item->bankItem->passage_html !!}
                        </article>
                    @endforeach
                </div>
                <div class="overflow-y-auto p-6 bg-slate-50 dark:bg-slate-950">
                    @include('livewire.pages.student._test_questions', ['flatQuestions' => $flatQuestions])
                </div>
            </div>

        @elseif($currentModule === 'writing')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 h-[calc(100vh-3.5rem)]">
                <div class="overflow-y-auto p-6 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                    @foreach($module->items as $item)
                        <article class="prose dark:prose-invert max-w-none mb-8">
                            <h2>Task {{ $item->bankItem->meta_json['task_number'] ?? 1 }}: {{ $item->bankItem->title }}</h2>
                            @if($item->bankItem->image_path)
                                <img src="{{ Storage::url($item->bankItem->image_path) }}" class="rounded-xl">
                            @endif
                            {!! $item->bankItem->prompt_html !!}
                            <p class="text-sm text-slate-500">Minimum: {{ $item->bankItem->meta_json['min_words'] ?? 150 }} words.</p>
                        </article>
                    @endforeach
                </div>
                <div class="overflow-y-auto p-6 bg-slate-50 dark:bg-slate-950 space-y-6">
                    @foreach($flatQuestions as $row)
                        @php $q = $row['q']; @endphp
                        <div>
                            <p class="text-xs font-bold text-indigo-600 mb-1">Task {{ $row['n'] }}</p>
                            <textarea
                                wire:change="saveAnswer({{ $q->id }}, $event.target.value)"
                                rows="20"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 font-serif"
                                placeholder="Write your response here..."
                            >{{ is_string($answers[$q->id] ?? null) ? $answers[$q->id] : '' }}</textarea>
                            <p class="text-xs text-slate-500 mt-1" x-data="{ words: 0 }"
                               x-init="$el.previousElementSibling.previousElementSibling.addEventListener('input', e => words = (e.target.value.match(/\S+/g)||[]).length)">
                                <span x-text="words"></span> words
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif($currentModule === 'speaking')
            @php
                // Build a JS-friendly script for the speaking player.
                $speakingParts = [];
                foreach ($module->items as $item) {
                    $meta = $item->bankItem->meta_json ?? [];
                    $partNumber = (int) ($meta['part_number'] ?? (count($speakingParts) + 1));
                    $questions = [];
                    foreach ($item->bankItem->groups as $group) {
                        foreach ($group->questions as $q) {
                            if ($q->prompt) $questions[] = ['n' => $q->q_number, 'text' => $q->prompt];
                        }
                    }
                    $speakingParts[] = [
                        'part'         => $partNumber,
                        'title'        => $item->bankItem->title,
                        'cueCard'      => $meta['cue_card'] ?? null,
                        'prepSeconds'  => $partNumber === 2 ? 60 : 0,
                        'answerSeconds'=> $partNumber === 2 ? 120 : 45,
                        'questions'    => $questions,
                    ];
                }
                $hasExistingRecording = !empty($attempt->speaking_audio_path);
            @endphp

            <div class="max-w-3xl mx-auto p-6"
                 x-data="speakingPlayer(@js($speakingParts), @js($hasExistingRecording), @js($attempt->speaking_audio_path ? Storage::url($attempt->speaking_audio_path) : null))"
                 x-init="init()">

                {{-- Idle: pre-test instructions --}}
                <div x-show="state === 'idle'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-4">
                    <h2 class="text-xl font-bold">IELTS Speaking Test</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        This is a recorded speaking exam. Click <strong>Start Speaking Test</strong> below.
                        Your microphone will be activated and the examiner's questions will be read aloud automatically.
                        Speak your answer clearly. The whole session is recorded as a single audio file and uploaded for instructor grading.
                    </p>

                    <ul class="text-sm text-slate-700 dark:text-slate-300 list-disc pl-6 space-y-1">
                        <li><strong>Part 1</strong> — 4 short questions on familiar topics (~4–5 min)</li>
                        <li><strong>Part 2</strong> — Cue card with 1 minute preparation, then speak for 1–2 minutes</li>
                        <li><strong>Part 3</strong> — 4 follow-up discussion questions (~4–5 min)</li>
                    </ul>

                    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 text-sm">
                        <p class="font-semibold mb-1">Before starting:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Allow microphone access when your browser asks.</li>
                            <li>Find a quiet room and use a good microphone or headset.</li>
                            <li>Do not refresh the page during the test.</li>
                        </ul>
                    </div>

                    @if($hasExistingRecording)
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3 text-sm">
                            <p class="font-semibold mb-1">A previous recording is on file.</p>
                            <audio controls class="w-full mt-2">
                                <source src="{{ Storage::url($attempt->speaking_audio_path) }}">
                            </audio>
                            <p class="text-xs mt-2">Starting again will replace it with a new recording.</p>
                        </div>
                    @endif

                    <button type="button" @click="start()"
                            class="rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-3">
                        Start Speaking Test
                    </button>

                    <p x-show="error" x-text="error" class="text-sm text-rose-600"></p>
                </div>

                {{-- In-progress test --}}
                <div x-show="state !== 'idle' && state !== 'uploading' && state !== 'done'" class="space-y-4">
                    {{-- Status header --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-xs uppercase font-bold text-indigo-600">
                                Part <span x-text="currentPart()?.part"></span>
                                <span x-show="currentPart()?.part !== 2 && currentQ()">
                                    — Question <span x-text="currentQIdx + 1"></span> of <span x-text="currentPart()?.questions.length"></span>
                                </span>
                            </p>
                            <p class="text-base font-semibold" x-text="currentPart()?.title"></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase"
                                  :class="recording ? 'text-rose-600' : 'text-slate-500'">
                                <span class="w-2.5 h-2.5 rounded-full"
                                      :class="recording ? 'bg-rose-600 animate-pulse' : 'bg-slate-400'"></span>
                                <span x-text="recording ? 'Recording' : 'Idle'"></span>
                            </span>
                            <span class="font-mono text-lg" x-text="formatTime(elapsed)"></span>
                        </div>
                    </div>

                    {{-- Active question / cue card --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-4">
                        {{-- TTS reading state --}}
                        <div x-show="state === 'reading'" class="flex items-center gap-3 text-sm text-indigo-700 dark:text-indigo-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-pulse" viewBox="0 0 24 24" fill="currentColor"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3a4.5 4.5 0 0 0-2.5-4.03v8.05A4.5 4.5 0 0 0 16.5 12z"/></svg>
                            <span>Examiner is asking the question…</span>
                        </div>

                        {{-- Part 1 / Part 3 question text --}}
                        <template x-if="state === 'reading' || state === 'answering'">
                            <div>
                                <template x-if="currentQ()">
                                    <p class="text-lg leading-relaxed" x-text="currentQ().text"></p>
                                </template>

                                <template x-if="state === 'answering' && currentPart()?.part !== 2">
                                    <div class="mt-4 flex items-center justify-between">
                                        <p class="text-xs text-slate-500">Speak your answer, then press <strong>Next Question</strong>.</p>
                                        <button type="button" @click="nextQuestion()"
                                                class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2">
                                            <span x-text="isLastQuestion() ? (isLastPart() ? 'Finish & Upload' : 'Continue to Part ' + (currentPart().part + 1)) : 'Next Question'"></span>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Part 2 cue card --}}
                        <template x-if="state === 'cue_reading' || state === 'preparing' || state === 'part2_answer'">
                            <div>
                                <p class="text-sm font-semibold mb-2">Cue Card</p>
                                <div class="rounded-xl border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 p-4 whitespace-pre-line text-sm" x-text="currentPart()?.cueCard"></div>

                                <div x-show="state === 'cue_reading'" class="mt-4 text-sm text-indigo-700 dark:text-indigo-300">
                                    Examiner is reading the cue card…
                                </div>

                                <div x-show="state === 'preparing'" class="mt-4">
                                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Preparation time</p>
                                    <p class="text-3xl font-mono font-bold" x-text="formatTime(prepLeft)"></p>
                                    <p class="text-xs text-slate-500">You may make brief notes on paper. The examiner will tell you when to start.</p>
                                    <button type="button" @click="startPart2Answer()"
                                            class="mt-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2">
                                        I'm ready — start answering now
                                    </button>
                                </div>

                                <div x-show="state === 'part2_answer'" class="mt-4">
                                    <p class="text-sm font-semibold text-rose-700 dark:text-rose-300">Speak now (1–2 minutes)</p>
                                    <p class="text-3xl font-mono font-bold" x-text="formatTime(answerLeft)"></p>
                                    <button type="button" @click="finishPart2()"
                                            class="mt-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2">
                                        Done — continue to Part 3
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="abort()"
                            class="text-xs text-rose-500 hover:underline">Cancel test (recording will be discarded)</button>
                </div>

                {{-- Uploading --}}
                <div x-show="state === 'uploading'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 text-center space-y-3">
                    <h3 class="text-lg font-semibold">Uploading your recording…</h3>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                        <div class="bg-indigo-600 h-full transition-all" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-sm text-slate-500" x-text="`${uploadProgress}%`"></p>
                </div>

                {{-- Done --}}
                <div x-show="state === 'done'" class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-6 space-y-3">
                    <h3 class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">Recording uploaded ✓</h3>
                    <p class="text-sm">Your speaking response has been saved for instructor evaluation.</p>
                    <template x-if="recordingUrl">
                        <audio controls class="w-full" :src="recordingUrl"></audio>
                    </template>
                    <button type="button" wire:click="finishModule"
                            class="rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-3">
                        Submit speaking module
                    </button>
                </div>

                {{-- Permission / error toast --}}
                <p x-show="error" x-text="error" class="mt-3 text-sm text-rose-600"></p>
            </div>
        @endif
    @endif

    {{-- Bottom navigation: module tabs --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 px-4 py-2 flex items-center gap-2 overflow-x-auto z-20">
        @foreach($mockTest->modules as $m)
            <span class="text-xs px-3 py-1 rounded-full whitespace-nowrap
                {{ $m->module === $currentModule ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                {{ ucfirst($m->module) }}
                @if($m->module === 'listening' && $attempt->listening_band) — Band {{ $attempt->listening_band }} @endif
                @if($m->module === 'reading' && $attempt->reading_band) — Band {{ $attempt->reading_band }} @endif
            </span>
        @endforeach
        <span class="ml-auto text-xs text-slate-500">Question palette → click a number above to jump.</span>
    </div>

    <script>
        // ─────────── Speaking Test Player ───────────
        function speakingPlayer(parts, hasExisting, existingUrl) {
            return {
                parts: parts,
                currentPartIdx: 0,
                currentQIdx: 0,
                state: 'idle',  // idle | reading | answering | cue_reading | preparing | part2_answer | uploading | done
                recording: false,
                elapsed: 0,
                elapsedTimer: null,
                prepLeft: 0,
                prepTimer: null,
                answerLeft: 0,
                answerTimer: null,
                recorder: null,
                chunks: [],
                stream: null,
                error: null,
                uploadProgress: 0,
                recordingUrl: existingUrl || null,

                init() {
                    if (hasExisting) this.state = 'done';
                    // Make sure browser voices are loaded
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.getVoices();
                        window.speechSynthesis.onvoiceschanged = () => {};
                    }
                    window.addEventListener('speaking-upload-complete', () => {
                        this.state = 'done';
                        this.uploadProgress = 100;
                    });
                    window.addEventListener('speaking-upload-failed', (e) => {
                        this.error = (e.detail && e.detail.message) || 'Upload failed.';
                        this.state = 'idle';
                    });
                    window.addEventListener('speaking-submit-blocked', (e) => {
                        this.error = (e.detail && e.detail.message) || 'Recording required.';
                    });
                },

                currentPart() { return this.parts[this.currentPartIdx] || null; },
                currentQ() {
                    const p = this.currentPart(); if (!p) return null;
                    return p.questions[this.currentQIdx] || null;
                },
                isLastQuestion() {
                    const p = this.currentPart(); if (!p) return true;
                    return this.currentQIdx >= (p.questions.length - 1);
                },
                isLastPart() { return this.currentPartIdx >= this.parts.length - 1; },

                async start() {
                    this.error = null;
                    if (!('speechSynthesis' in window)) {
                        this.error = 'Your browser does not support speech synthesis. Please use Chrome, Edge or Safari.';
                        return;
                    }
                    if (!navigator.mediaDevices || !window.MediaRecorder) {
                        this.error = 'Audio recording is not supported in this browser.';
                        return;
                    }
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    } catch (e) {
                        const name = (e && e.name) || 'Error';
                        let hint = '';
                        if (name === 'NotAllowedError' || name === 'SecurityError') {
                            hint = 'Microphone permission was blocked for this site. Click the lock/info icon in your browser address bar, set Microphone to "Allow", then reload the page.';
                        } else if (name === 'NotFoundError' || name === 'OverconstrainedError') {
                            hint = 'No microphone was detected. Plug in a microphone or headset and try again.';
                        } else if (name === 'NotReadableError') {
                            hint = 'Your microphone is being used by another application. Close it and try again.';
                        } else if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                            hint = 'This page is not on HTTPS, so the browser will not allow microphone access. Please access this site via HTTPS or localhost.';
                        } else {
                            hint = 'Could not access microphone (' + name + ').';
                        }
                        this.error = hint + ' [' + name + (e && e.message ? ': ' + e.message : '') + ']';
                        console.error('[speaking] getUserMedia failed', e);
                        return;
                    }

                    this.chunks = [];
                    const mime = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus'
                              : MediaRecorder.isTypeSupported('audio/webm')             ? 'audio/webm'
                              : MediaRecorder.isTypeSupported('audio/ogg')              ? 'audio/ogg'
                              : '';
                    try {
                        this.recorder = new MediaRecorder(this.stream, mime ? { mimeType: mime } : {});
                    } catch (e) {
                        this.recorder = new MediaRecorder(this.stream);
                    }
                    this.recorder.ondataavailable = (e) => { if (e.data && e.data.size > 0) this.chunks.push(e.data); };
                    this.recorder.onstop = () => this.handleRecordingStopped();
                    this.recorder.start(1000);
                    this.recording = true;
                    this.elapsed = 0;
                    this.elapsedTimer = setInterval(() => this.elapsed++, 1000);
                    this.runPart();
                },

                runPart() {
                    const p = this.currentPart();
                    if (!p) { this.finishExam(); return; }
                    if (p.part === 2) this.runPart2();
                    else this.askQuestion();
                },

                askQuestion() {
                    const q = this.currentQ();
                    if (!q) { this.advancePart(); return; }
                    this.state = 'reading';
                    this.speak(q.text, () => { this.state = 'answering'; });
                },

                nextQuestion() {
                    window.speechSynthesis.cancel();
                    const p = this.currentPart();
                    if (this.currentQIdx + 1 < p.questions.length) {
                        this.currentQIdx++;
                        this.askQuestion();
                    } else {
                        this.advancePart();
                    }
                },

                advancePart() {
                    if (this.currentPartIdx + 1 < this.parts.length) {
                        this.currentPartIdx++;
                        this.currentQIdx = 0;
                        this.runPart();
                    } else {
                        this.finishExam();
                    }
                },

                runPart2() {
                    const p = this.currentPart();
                    const intro = "Now I'd like you to talk about a topic for one to two minutes. Before you talk, you have one minute to think about what you're going to say. You can make some notes if you wish. Here is the cue card.";
                    this.state = 'cue_reading';
                    const full = intro + ' ' + (p.cueCard || '');
                    this.speak(full, () => { this.startPrepTimer(); });
                },

                startPrepTimer() {
                    this.prepLeft = this.currentPart().prepSeconds || 60;
                    this.state = 'preparing';
                    if (this.prepTimer) clearInterval(this.prepTimer);
                    this.prepTimer = setInterval(() => {
                        this.prepLeft--;
                        if (this.prepLeft <= 0) {
                            clearInterval(this.prepTimer); this.prepTimer = null;
                            this.startPart2Answer();
                        }
                    }, 1000);
                },

                startPart2Answer() {
                    if (this.prepTimer) { clearInterval(this.prepTimer); this.prepTimer = null; }
                    this.answerLeft = this.currentPart().answerSeconds || 120;
                    this.state = 'part2_answer';
                    this.speak("All right? Remember, you have one to two minutes for this. Please start speaking now.", () => {});
                    if (this.answerTimer) clearInterval(this.answerTimer);
                    this.answerTimer = setInterval(() => {
                        this.answerLeft--;
                        if (this.answerLeft <= 0) {
                            clearInterval(this.answerTimer); this.answerTimer = null;
                            this.finishPart2();
                        }
                    }, 1000);
                },

                finishPart2() {
                    if (this.answerTimer) { clearInterval(this.answerTimer); this.answerTimer = null; }
                    window.speechSynthesis.cancel();
                    this.advancePart();
                },

                finishExam() {
                    this.cleanupTimers();
                    window.speechSynthesis.cancel();
                    this.state = 'uploading';
                    this.uploadProgress = 0;
                    try { this.recorder && this.recorder.state !== 'inactive' && this.recorder.stop(); } catch (e) {}
                },

                handleRecordingStopped() {
                    this.recording = false;
                    if (this.stream) this.stream.getTracks().forEach(t => t.stop());
                    const type = (this.recorder && this.recorder.mimeType) || 'audio/webm';
                    const ext = type.includes('ogg') ? 'ogg' : 'webm';
                    const blob = new Blob(this.chunks, { type });
                    this.recordingUrl = URL.createObjectURL(blob);
                    const file = new File([blob], `speaking.${ext}`, { type });
                    // Upload via Livewire
                    this.$wire.upload(
                        'speakingRecording',
                        file,
                        () => { this.$wire.call('saveSpeakingRecording'); },
                        (err) => { this.error = 'Upload failed.'; this.state = 'idle'; console.error(err); },
                        (ev) => { if (ev && typeof ev.detail?.progress === 'number') this.uploadProgress = ev.detail.progress; }
                    );
                },

                abort() {
                    if (!confirm('Cancel the speaking test? Your recording will be discarded.')) return;
                    this.cleanupTimers();
                    window.speechSynthesis.cancel();
                    try { this.recorder && this.recorder.state !== 'inactive' && this.recorder.stop(); } catch (e) {}
                    if (this.stream) this.stream.getTracks().forEach(t => t.stop());
                    this.recording = false;
                    this.state = 'idle';
                    this.elapsed = 0;
                    this.currentPartIdx = 0;
                    this.currentQIdx = 0;
                    this.chunks = [];
                },

                cleanupTimers() {
                    if (this.elapsedTimer) clearInterval(this.elapsedTimer);
                    if (this.prepTimer)    clearInterval(this.prepTimer);
                    if (this.answerTimer)  clearInterval(this.answerTimer);
                    this.elapsedTimer = this.prepTimer = this.answerTimer = null;
                },

                speak(text, onEnd) {
                    if (!('speechSynthesis' in window)) { if (onEnd) onEnd(); return; }
                    window.speechSynthesis.cancel();
                    const voices = window.speechSynthesis.getVoices();
                    const voice = voices.find(v => /en-GB/i.test(v.lang) && /female|natural|google|microsoft/i.test(v.name)) ||
                                  voices.find(v => /en-GB/i.test(v.lang)) ||
                                  voices.find(v => /en-US/i.test(v.lang)) ||
                                  voices.find(v => /^en/i.test(v.lang)) || null;
                    const chunks = text.match(/[^.!?\n]+[.!?\n]+|[^.!?\n]+$/g) || [text];
                    let i = 0;
                    const speakNext = () => {
                        if (i >= chunks.length) { if (onEnd) onEnd(); return; }
                        const u = new SpeechSynthesisUtterance(chunks[i].trim());
                        if (voice) u.voice = voice;
                        u.lang = voice ? voice.lang : 'en-GB';
                        u.rate = 1;
                        u.pitch = 1;
                        u.onend = () => { i++; speakNext(); };
                        u.onerror = () => { if (onEnd) onEnd(); };
                        window.speechSynthesis.speak(u);
                    };
                    speakNext();
                },

                formatTime(s) {
                    s = Math.max(0, parseInt(s) || 0);
                    const m = Math.floor(s / 60).toString().padStart(2, '0');
                    const sec = (s % 60).toString().padStart(2, '0');
                    return `${m}:${sec}`;
                }
            }
        }

        function ttsPlayer(text, id) {
            return {
                text: text,
                id: id,
                speaking: false,
                paused: false,
                rate: 1,
                utter: null,
                preferredVoice: null,
                pickVoice() {
                    const voices = window.speechSynthesis.getVoices();
                    // Prefer en-GB / en-US natural voices
                    this.preferredVoice =
                        voices.find(v => /en-GB/i.test(v.lang) && /female|natural|google|microsoft/i.test(v.name)) ||
                        voices.find(v => /en-GB/i.test(v.lang)) ||
                        voices.find(v => /en-US/i.test(v.lang) && /natural|google|microsoft/i.test(v.name)) ||
                        voices.find(v => /^en/i.test(v.lang)) ||
                        voices[0] || null;
                },
                play() {
                    if (!('speechSynthesis' in window)) { alert('Your browser does not support speech synthesis. Please use Chrome, Edge, or Safari.'); return; }
                    window.speechSynthesis.cancel();
                    this.pickVoice();
                    // Split into manageable chunks to avoid TTS engine cutoffs (~200 char limit on some browsers)
                    const chunks = this.text.match(/[^.!?\n]+[.!?\n]+|[^.!?\n]+$/g) || [this.text];
                    let i = 0;
                    const speakNext = () => {
                        if (i >= chunks.length) { this.speaking = false; this.paused = false; return; }
                        const u = new SpeechSynthesisUtterance(chunks[i].trim());
                        if (this.preferredVoice) u.voice = this.preferredVoice;
                        u.lang = this.preferredVoice ? this.preferredVoice.lang : 'en-GB';
                        u.rate = this.rate;
                        u.pitch = 1;
                        u.onend = () => { i++; speakNext(); };
                        u.onerror = () => { this.speaking = false; this.paused = false; };
                        this.utter = u;
                        window.speechSynthesis.speak(u);
                    };
                    this.speaking = true;
                    this.paused = false;
                    speakNext();
                },
                pause() { window.speechSynthesis.pause(); this.paused = true; },
                resume() { window.speechSynthesis.resume(); this.paused = false; },
                stop() { window.speechSynthesis.cancel(); this.speaking = false; this.paused = false; },
            }
        }

        // Some browsers populate voices async
        if ('speechSynthesis' in window) {
            window.speechSynthesis.onvoiceschanged = () => {};
        }

        function ieltsTimer(endsAt, attemptId) {
            return {
                endsAt: endsAt,
                timeLeft: 0,
                tick: null,
                init() {
                    this.recompute();
                    this.tick = setInterval(() => this.recompute(), 1000);
                },
                recompute() {
                    if (!this.endsAt) { this.timeLeft = 0; return; }
                    this.timeLeft = Math.max(0, this.endsAt - Math.floor(Date.now()/1000));
                    if (this.timeLeft === 0 && this.endsAt) {
                        clearInterval(this.tick);
                        // auto-submit module
                        Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))?.call('finishModule');
                    }
                },
                formatTime(s) {
                    const m = Math.floor(s / 60).toString().padStart(2, '0');
                    const sec = (s % 60).toString().padStart(2, '0');
                    return `${m}:${sec}`;
                }
            }
        }
    </script>
</div>
