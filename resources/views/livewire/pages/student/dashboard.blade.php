<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Student Dashboard</h2>
        <p class="text-sm text-slate-500 mt-1">Start mock tests, track scores, and improve over time.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            <h3 class="text-xl font-semibold mb-4">Welcome back, {{ $user->name }}!</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-xl bg-indigo-50/70 dark:bg-indigo-900/20 p-5 border border-indigo-100 dark:border-indigo-900">
                    <h4 class="text-indigo-700 dark:text-indigo-300 font-semibold">Average Score</h4>
                    <p class="text-4xl font-bold mt-2 text-indigo-600 dark:text-indigo-300">{{ number_format($averageScore, 1) }}</p>
                </div>

                <div class="rounded-xl bg-slate-50/70 dark:bg-slate-800/50 p-5 border border-slate-200 dark:border-slate-700">
                    <h4 class="font-semibold mb-3">Recent Tests</h4>
                    @if(count($recentAttempts) > 0)
                        <ul class="space-y-2 text-sm">
                            @foreach($recentAttempts as $attempt)
                                <li class="flex justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
                                    <span>{{ $attempt->mockTest->title ?? 'Mock Test' }}</span>
                                    <span class="font-medium">{{ $attempt->raw_score }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-slate-500 text-sm">No recent test attempts found.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Available Published Mock Tests</h3>
            <div class="space-y-3">
                @forelse($availableTests as $test)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="font-semibold">{{ $test->title }}</p>
                            <p class="text-sm text-slate-500">Duration: {{ $test->duration_minutes }} minutes • Sections: {{ $test->sections->count() }}</p>
                        </div>
                        <a href="{{ route('student.test.attempt', $test->id) }}" class="rounded-lg px-4 py-2 bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition">Start Test</a>
                    </div>
                @empty
                    <p class="text-slate-500">No published tests available yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
