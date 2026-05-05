<div class="p-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h2 class="text-2xl font-semibold">Available Published Mock Tests</h2>
        <p class="text-sm text-slate-500 mt-1">Choose a test and begin immediately. Each test includes timed sections to mirror the real exam.</p>

        <div class="mt-6 space-y-4">
            @forelse($availableTests as $test)
                <div class="rounded-xl border p-4 flex items-center justify-between">
                    <div>
                        <div class="text-lg font-bold">{{ $test->title }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ $test->modules->count() }} modules · {{ $test->duration ?? '—' }} min</div>
                    </div>
                    <div>
                        <a href="{{ route('student.test.attempt', $test->id) }}" class="rounded-xl px-4 py-2 bg-indigo-600 text-white">Start Test</a>
                    </div>
                </div>
            @empty
                <div class="text-slate-500">No published mock tests are available.</div>
            @endforelse
        </div>
    </div>
</div>
