<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Instructor Dashboard</h2>
        <p class="text-sm text-slate-500 mt-1">Manage test content and monitor pending evaluations.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 transition hover:-translate-y-1">
                <p class="text-sm text-slate-500">Pending Evaluations</p>
                <p class="text-3xl font-bold mt-2">{{ $evaluationsPending }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 transition hover:-translate-y-1">
                <p class="text-sm text-slate-500">My Created Tests</p>
                <p class="text-3xl font-bold mt-2">{{ $myTests }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
            <h3 class="font-semibold mb-3">Published Mock Tests</h3>
            @forelse($publishedTests as $test)
                <div class="py-2 border-b border-slate-200 dark:border-slate-700 flex justify-between text-sm">
                    <span>{{ $test->title }}</span>
                    <span>{{ $test->duration_minutes }} mins</span>
                </div>
            @empty
                <p class="text-slate-500 text-sm">No published mock tests yet.</p>
            @endforelse
        </div>
    </div>
</div>
