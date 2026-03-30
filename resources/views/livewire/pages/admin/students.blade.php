<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Students</h2>
        <p class="text-sm text-slate-500 mt-1">Dedicated student directory with effort analytics for owner monitoring.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Total Students</p>
                <p class="text-3xl font-bold mt-2">{{ $totalStudents }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Active This Week</p>
                <p class="text-3xl font-bold mt-2">{{ $activeThisWeek }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Avg Attempts / Student</p>
                <p class="text-3xl font-bold mt-2">{{ $avgAttempts }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-6">
            <div class="mb-4">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search student by name or email..." class="w-full md:w-[28rem] rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300">
                            <th class="py-3 px-4 font-semibold">Student</th>
                            <th class="py-3 px-4 font-semibold">Attempts</th>
                            <th class="py-3 px-4 font-semibold">Avg Score</th>
                            <th class="py-3 px-4 font-semibold">Pending Evaluation</th>
                            <th class="py-3 px-4 font-semibold">Effort</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($students as $student)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition-colors">
                                <td class="py-3 px-4">
                                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->email }}</p>
                                </td>
                                <td class="py-3 px-4">{{ $student->total_attempts }}</td>
                                <td class="py-3 px-4">{{ $student->avg_raw_score }}</td>
                                <td class="py-3 px-4">{{ $student->pending_evaluation_attempts }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">{{ $student->effort_score }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $student->effort_level === 'High' ? 'bg-emerald-100 text-emerald-700' : ($student->effort_level === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $student->effort_level }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button wire:click="viewStudent({{ $student->id }})" class="rounded-lg px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">View Analysis</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($showDetailsModal && $selectedStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:click.self="$set('showDetailsModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-3xl p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold">{{ $selectedStudent->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $selectedStudent->email }}</p>
                    </div>
                    <button wire:click="$set('showDetailsModal', false)" class="text-slate-400 hover:text-slate-600">X</button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5 text-sm">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Total Attempts</p><p class="text-xl font-bold">{{ $selectedStudent->total_attempts }}</p></div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Completed</p><p class="text-xl font-bold">{{ $selectedStudent->completed_attempts }}</p></div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Avg Score</p><p class="text-xl font-bold">{{ round((float)($selectedStudent->avg_raw_score ?? 0), 2) }}</p></div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Effort</p><p class="text-xl font-bold">{{ $selectedStudent->effort_score }} ({{ $selectedStudent->effort_level }})</p></div>
                </div>

                <h4 class="font-semibold mb-2">Recent Attempts</h4>
                <div class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr>
                                <th class="text-left py-2 px-3">Mock Test</th>
                                <th class="text-left py-2 px-3">Status</th>
                                <th class="text-left py-2 px-3">Score</th>
                                <th class="text-left py-2 px-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedStudent->attempts as $attempt)
                                <tr class="border-t border-slate-100 dark:border-slate-700">
                                    <td class="py-2 px-3">{{ $attempt->mockTest->title ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">
                                        @if($attempt->status === 'pending_evaluation')
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold uppercase tracking-wider">Pending</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold uppercase tracking-wider">{{ ucfirst($attempt->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 font-mono font-bold">{{ $attempt->raw_score ?? '-' }}</td>
                                    <td class="py-2 px-3 flex items-center gap-2">
                                        <div class="flex-1">{{ optional($attempt->created_at)->format('d M Y') }}</div>
                                        @if($attempt->status === 'pending_evaluation')
                                            @if($gradingAttemptId === $attempt->id)
                                                <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800 p-2 rounded-lg border border-indigo-200">
                                                    <input type="number" wire:model="gradingScore" placeholder="Score" class="w-20 rounded-md border-slate-300 text-xs" />
                                                    <select wire:model="gradingBand" class="w-20 rounded-md border-slate-300 text-xs">
                                                        @foreach(['5.0','5.5','6.0','6.5','7.0','7.5','8.0','8.5','9.0'] as $band)
                                                            <option value="{{ $band }}">{{ $band }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button wire:click="submitGrade" class="px-2 py-1 bg-emerald-600 text-white rounded text-xs">Save</button>
                                                    <button wire:click="cancelGrading" class="px-2 py-1 bg-slate-500 text-white rounded text-xs">X</button>
                                                </div>
                                            @else
                                                <button wire:click="startGrading({{ $attempt->id }}, {{ $attempt->raw_score ?? 0 }})" class="px-2 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700 transition">Grade</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 px-3 text-slate-500">No attempts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
