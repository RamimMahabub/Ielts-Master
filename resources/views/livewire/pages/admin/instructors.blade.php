<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Instructors</h2>
        <p class="text-sm text-slate-500 mt-1">Separate instructor directory with contribution and effort analytics for owner review.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Total Instructors</p>
                <p class="text-3xl font-bold mt-2">{{ $totalInstructors }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Approved</p>
                <p class="text-3xl font-bold mt-2">{{ $approvedInstructors }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Pending</p>
                <p class="text-3xl font-bold mt-2">{{ $pendingInstructors }}</p>
            </div>
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-5">
                <p class="text-sm text-slate-500">Avg Created Tests</p>
                <p class="text-3xl font-bold mt-2">{{ $avgCreatedTests }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-white/60 dark:border-slate-800 shadow p-6">
            <div class="mb-4">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search instructor by name or email..." class="w-full md:w-[28rem] rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300">
                            <th class="py-3 px-4 font-semibold">Instructor</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Created Tests</th>
                            <th class="py-3 px-4 font-semibold">Published</th>
                            <th class="py-3 px-4 font-semibold">Pending Reviews</th>
                            <th class="py-3 px-4 font-semibold">Effort</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($instructors as $instructor)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition-colors">
                                <td class="py-3 px-4">
                                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $instructor->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $instructor->email }}</p>
                                </td>
                                <td class="py-3 px-4">{{ ucfirst($instructor->instructor_status) }}</td>
                                <td class="py-3 px-4">{{ $instructor->total_tests_created }}</td>
                                <td class="py-3 px-4">{{ $instructor->published_tests_count }}</td>
                                <td class="py-3 px-4">{{ $instructor->pending_reviews_count }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">{{ $instructor->effort_score }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $instructor->effort_level === 'High' ? 'bg-emerald-100 text-emerald-700' : ($instructor->effort_level === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $instructor->effort_level }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button wire:click="viewInstructor({{ $instructor->id }})" class="rounded-lg px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">View Analysis</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-500">No instructors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($showDetailsModal && $selectedInstructor)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:click.self="$set('showDetailsModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-3xl p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold">{{ $selectedInstructor->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $selectedInstructor->email }}</p>
                    </div>
                    <button wire:click="$set('showDetailsModal', false)" class="text-slate-400 hover:text-slate-600">X</button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5 text-sm">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Status</p><p class="text-xl font-bold">{{ ucfirst($selectedInstructor->instructor_status) }}</p></div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Created Tests</p><p class="text-xl font-bold">{{ $selectedInstructor->total_tests_created }}</p></div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Published</p><p class="text-xl font-bold">{{ $selectedInstructor->published_tests_count }}</p></div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3"><p class="text-slate-500">Effort</p><p class="text-xl font-bold">{{ $selectedInstructor->effort_score }} ({{ $selectedInstructor->effort_level }})</p></div>
                </div>

                <h4 class="font-semibold mb-2">Recent Created Mock Tests</h4>
                <div class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr>
                                <th class="text-left py-2 px-3">Title</th>
                                <th class="text-left py-2 px-3">Duration</th>
                                <th class="text-left py-2 px-3">Published</th>
                                <th class="text-left py-2 px-3">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedInstructor->createdMockTests as $test)
                                <tr class="border-t border-slate-100 dark:border-slate-700">
                                    <td class="py-2 px-3">{{ $test->title }}</td>
                                    <td class="py-2 px-3">{{ $test->duration_minutes }} mins</td>
                                    <td class="py-2 px-3">{{ $test->is_published ? 'Yes' : 'No' }}</td>
                                    <td class="py-2 px-3">{{ optional($test->created_at)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 px-3 text-slate-500">No created tests yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
