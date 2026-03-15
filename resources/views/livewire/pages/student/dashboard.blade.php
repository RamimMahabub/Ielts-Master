<div>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Student Dashboard</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">Start mock tests, track progress, and build confidence section by section.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="relative overflow-hidden rounded-3xl border border-slate-200/70 bg-gradient-to-br from-sky-50 via-white to-emerald-50 p-6 shadow-sm dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 md:p-8">
            <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-sky-200/40 blur-3xl dark:bg-sky-500/10"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-16 h-44 w-44 rounded-full bg-emerald-200/40 blur-3xl dark:bg-emerald-500/10"></div>

            <div class="relative flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <p class="inline-flex w-fit items-center rounded-full border border-sky-200 bg-sky-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-sky-700 dark:border-sky-800 dark:bg-sky-900/40 dark:text-sky-200">
                        Welcome Back
                    </p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white md:text-3xl">Hi, {{ $user->name }}. Ready for your next mock test?</h3>
                    <p class="max-w-2xl text-sm text-slate-600 dark:text-slate-300">Your dashboard gives you a quick snapshot of performance and available tests so you can continue your IELTS prep without friction.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <article class="rounded-2xl border border-sky-200/70 bg-white/80 p-5 shadow-sm backdrop-blur dark:border-sky-900 dark:bg-slate-900/70">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700 dark:text-sky-300">Average Score</p>
                                <p class="mt-2 text-4xl font-bold text-sky-700 dark:text-sky-200">{{ number_format($averageScore, 1) }}</p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Across your completed attempts</p>
                            </div>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l4.5-4.5 4 4L21 3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 3H21v3.5" />
                                </svg>
                            </span>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/70">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-700 dark:text-slate-300">Recent Tests</p>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Last {{ count($recentAttempts) }}</span>
                        </div>
                        @if(count($recentAttempts) > 0)
                            <ul class="space-y-2 text-sm">
                                @foreach($recentAttempts as $attempt)
                                    <li class="flex items-center justify-between rounded-lg border border-slate-200/80 bg-slate-50/70 px-3 py-2 dark:border-slate-700 dark:bg-slate-800/60">
                                        <span class="truncate pr-3 text-slate-700 dark:text-slate-200">{{ $attempt->mockTest->title ?? 'Mock Test' }}</span>
                                        <span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">{{ $attempt->raw_score }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-4 text-sm text-slate-500 dark:border-slate-600 dark:bg-slate-800/40 dark:text-slate-400">No recent test attempts found.</p>
                        @endif
                    </article>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
            <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Available Published Mock Tests</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Choose a test and begin immediately. Each test includes timed sections to mirror the real exam.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ count($availableTests) }} {{ count($availableTests) === 1 ? 'test' : 'tests' }}</span>
            </div>

            <div class="space-y-3">
                @forelse($availableTests as $test)
                    <article class="group rounded-2xl border border-slate-200 bg-gradient-to-r from-white via-white to-slate-50 p-4 transition hover:border-sky-300 hover:shadow-md dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800/80 dark:hover:border-sky-700 md:p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <p class="truncate text-lg font-semibold text-slate-900 dark:text-white">{{ $test->title }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-300">
                                    <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 dark:border-slate-600 dark:bg-slate-800">{{ $test->duration_minutes }} min</span>
                                    <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 dark:border-slate-600 dark:bg-slate-800">{{ $test->sections->count() }} {{ $test->sections->count() === 1 ? 'section' : 'sections' }}</span>
                                </div>
                            </div>

                            <a href="{{ route('student.test.attempt', $test->id) }}" class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-900">
                                Start Test
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center dark:border-slate-600 dark:bg-slate-800/40">
                        <p class="text-sm text-slate-600 dark:text-slate-300">No published tests available yet.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
