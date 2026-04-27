<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Test History Archive</h2>
        <p class="text-sm text-slate-500 mt-1">Review completed and pending evaluation attempts.</p>
    </x-slot>

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
        @if($attempts->isEmpty())
            <p class="text-slate-600">No attempts yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                            <th class="py-2 pr-4">Test</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">L</th>
                            <th class="py-2 pr-4">R</th>
                            <th class="py-2 pr-4">W</th>
                            <th class="py-2 pr-4">S</th>
                            <th class="py-2 pr-4">Overall</th>
                            <th class="py-2 pr-4">Date</th>
                            <th class="py-2">Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <td class="py-2 pr-4">{{ $attempt->mockTest->title ?? 'Mock Test' }}</td>
                                <td class="py-2 pr-4 capitalize">{{ str_replace('_', ' ', $attempt->status) }}</td>
                                <td class="py-2 pr-4">{{ $attempt->listening_band ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $attempt->reading_band ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $attempt->writing_band ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $attempt->speaking_band ?? '-' }}</td>
                                <td class="py-2 pr-4 font-semibold">{{ $attempt->overall_band ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-2">
                                    @if($attempt->status === 'completed' && $attempt->overall_band !== null)
                                        <a href="{{ route('student.reports.band_score', $attempt) }}" target="_blank" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">
                                            Download PDF
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">After grading</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
