<div>
    <div wire:poll.5s="refreshNotifications"></div>

    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Student Dashboard</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">Start mock tests, track progress, and build confidence section by section.</p>
        </div>
    </x-slot>

    
    <div class="space-y-6">
        @if($notifications && $notifications->count() > 0)
            <section class="rounded-3xl border border-indigo-100 bg-indigo-50/50 p-4 dark:border-indigo-900/30 dark:bg-indigo-900/10">
                <div class="flex items-center justify-between mb-3 px-2">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Recent Alerts</h3>
                    <span class="text-xs font-semibold bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-0.5 rounded-full">{{ $notifications->count() }} New</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($notifications as $notification)
                        <div class="relative flex items-start gap-4 rounded-2xl border border-white/60 bg-white/80 p-4 shadow-sm backdrop-blur transition hover:border-indigo-200 dark:border-slate-800 dark:bg-slate-900/80 dark:hover:border-indigo-900">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $notification->data['type'] === 'new_mock_test' ? 'bg-sky-100 text-sky-600 dark:bg-sky-900/30' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' }}">
                                @if($notification->data['type'] === 'new_mock_test')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $notification->data['title'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-400">{{ $notification->data['message'] }}</p>
                                <div class="mt-2 flex items-center gap-3">
                                    <a href="{{ $notification->data['link'] }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">View Details</a>
                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-medium text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300">Dismiss</button>
                                </div>
                            </div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap">{{ $notification->created_at->diffForHumans(null, true) }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
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
                                        <span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">{{ $attempt->overall_band ? 'Band '.$attempt->overall_band : ucfirst(str_replace('_',' ',$attempt->status)) }}</span>
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
                                    <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 dark:border-slate-600 dark:bg-slate-800">{{ $test->modules->sum('duration_minutes') }} min</span>
                                    <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 dark:border-slate-600 dark:bg-slate-800">{{ $test->modules->count() }} modules</span>
                                    <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 dark:border-slate-600 dark:bg-slate-800 capitalize">{{ $test->test_type }}</span>
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
