<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Admin Dashboard</h2>
        <p class="text-sm text-slate-500 mt-1">Manage users, instructor verification, question bank, and mock tests.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 transition hover:-translate-y-1">
                <p class="text-sm text-slate-500">Total Users</p>
                <p class="text-3xl font-bold mt-2">{{ $totalUsers }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 transition hover:-translate-y-1">
                <p class="text-sm text-slate-500">Total Mock Tests</p>
                <p class="text-3xl font-bold mt-2">{{ $totalTests }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 transition hover:-translate-y-1">
                <p class="text-sm text-slate-500">Total Attempts</p>
                <p class="text-3xl font-bold mt-2">{{ $totalAttempts }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5 transition hover:-translate-y-1">
                <p class="text-sm text-slate-500">Pending Instructors</p>
                <p class="text-3xl font-bold mt-2">{{ $pendingInstructorRequests }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="text-lg font-semibold mb-3">Quick Actions</h3>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('admin.users') }}" class="block rounded-xl px-4 py-2 bg-slate-100/70 dark:bg-slate-800/70 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Manage Users</a>
                    <a href="{{ route('admin.instructor.verification') }}" class="block rounded-xl px-4 py-2 bg-slate-100/70 dark:bg-slate-800/70 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Approve Instructors</a>
                    <a href="{{ route('admin.question_bank') }}" class="block rounded-xl px-4 py-2 bg-slate-100/70 dark:bg-slate-800/70 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Question Bank CRUD</a>
                    <a href="{{ route('admin.mock_test') }}" class="block rounded-xl px-4 py-2 bg-slate-100/70 dark:bg-slate-800/70 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Build Mock Test</a>
                </div>
            </div>

            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
                <h3 class="text-lg font-semibold mb-3">Recent Attempts</h3>
                @if($recentAttempts->isEmpty())
                    <p class="text-slate-500 text-sm">No attempts yet.</p>
                @else
                    <ul class="space-y-2 text-sm">
                        @foreach($recentAttempts as $attempt)
                            <li class="flex justify-between border-b border-slate-200/70 dark:border-slate-700/70 pb-2">
                                <span>{{ $attempt->user->name }} - {{ $attempt->mockTest->title ?? 'Mock Test' }}</span>
                                <span class="font-medium">{{ $attempt->status }} / Band {{ $attempt->overall_band ?? '-' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
