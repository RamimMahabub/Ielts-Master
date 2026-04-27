<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Band Score Report - {{ $attempt->mockTest->title ?? 'Mock Test' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2f7; color: #0f172a; font-family: Arial, Helvetica, sans-serif; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 24px; background: #0f172a; color: #fff; }
        .toolbar a, .toolbar button { border: 0; border-radius: 8px; padding: 10px 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .toolbar a { background: #1e293b; color: #fff; }
        .toolbar button { background: #2563eb; color: #fff; }
        .page { max-width: 920px; margin: 28px auto; padding: 48px; background: #fff; border: 1px solid #dbe3ef; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12); }
        .brand { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #2563eb; padding-bottom: 18px; }
        .brand h1 { margin: 0; font-size: 30px; letter-spacing: 0; }
        .brand p { margin: 6px 0 0; color: #475569; }
        .badge { border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; background: #eff6ff; color: #1d4ed8; font-weight: 700; text-align: right; }
        .certificate { margin-top: 34px; border: 2px solid #cbd5e1; padding: 28px; text-align: center; }
        .certificate h2 { margin: 0; font-size: 24px; }
        .student { margin: 18px 0 8px; font-size: 34px; font-weight: 800; }
        .test-title { color: #475569; font-size: 16px; }
        .overall { display: inline-flex; flex-direction: column; margin-top: 24px; min-width: 170px; border-radius: 14px; background: #0f172a; color: #fff; padding: 18px 24px; }
        .overall span:first-child { font-size: 13px; text-transform: uppercase; color: #cbd5e1; }
        .overall span:last-child { font-size: 54px; font-weight: 800; line-height: 1; margin-top: 8px; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 28px; }
        .score-card { border: 1px solid #dbe3ef; border-radius: 10px; padding: 14px; background: #f8fafc; }
        .score-card p { margin: 0; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; }
        .score-card strong { display: block; margin-top: 8px; font-size: 28px; }
        .section { margin-top: 30px; }
        .section h3 { margin: 0 0 12px; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { border: 1px solid #dbe3ef; padding: 10px; text-align: left; }
        th { background: #f1f5f9; color: #334155; }
        .meta { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 24px; margin-top: 24px; color: #334155; font-size: 14px; }
        .footer { margin-top: 34px; padding-top: 18px; border-top: 1px solid #dbe3ef; color: #64748b; font-size: 12px; display: flex; justify-content: space-between; gap: 18px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { margin: 0; max-width: none; min-height: 100vh; border: 0; box-shadow: none; padding: 32px; }
        }
    </style>
</head>
<body>
    @if(empty($isPdf))
        <div class="toolbar">
            <a href="{{ route('student.history') }}">Back to Test History</a>
            <button type="button" onclick="window.print()">Download PDF</button>
        </div>
    @endif

    <main class="page">
        <header class="brand">
            <div>
                <h1>IELTS Master</h1>
                <p>Mock Test Band Score Report & Certificate</p>
            </div>
            <div class="badge">Generated<br>{{ now()->format('M d, Y') }}</div>
        </header>

        <section class="certificate">
            <h2>Certificate of Mock Test Completion</h2>
            <div class="student">{{ $attempt->user->name }}</div>
            <div class="test-title">{{ $attempt->mockTest->title ?? 'IELTS Mock Test' }}</div>
            <div class="overall">
                <span>Overall Band</span>
                <span>{{ number_format((float) $attempt->overall_band, 1) }}</span>
            </div>
        </section>

        <section class="grid">
            <div class="score-card"><p>Listening</p><strong>{{ $attempt->listening_band ?? '-' }}</strong></div>
            <div class="score-card"><p>Reading</p><strong>{{ $attempt->reading_band ?? '-' }}</strong></div>
            <div class="score-card"><p>Writing</p><strong>{{ $attempt->writing_band ?? '-' }}</strong></div>
            <div class="score-card"><p>Speaking</p><strong>{{ $attempt->speaking_band ?? '-' }}</strong></div>
        </section>

        <section class="meta">
            <div><strong>Student Email:</strong> {{ $attempt->user->email }}</div>
            <div><strong>Attempt Status:</strong> {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}</div>
            <div><strong>Started:</strong> {{ optional($attempt->started_at)->format('M d, Y h:i A') ?? '-' }}</div>
            <div><strong>Completed:</strong> {{ optional($attempt->completed_at)->format('M d, Y h:i A') ?? '-' }}</div>
        </section>

        <section class="section">
            <h3>Auto-Graded Accuracy</h3>
            <table>
                <thead>
                    <tr><th>Correct Answers</th><th>Total Auto-Graded</th><th>Accuracy</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $correctAnswers }}</td>
                        <td>{{ $autoGradedTotal }}</td>
                        <td>{{ $autoGradedTotal > 0 ? number_format(($correctAnswers / $autoGradedTotal) * 100, 1) . '%' : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h3>Module Question Review</h3>
            <table>
                <thead>
                    <tr><th>Module</th><th>Answered</th><th>Correct</th><th>Needs Review</th></tr>
                </thead>
                <tbody>
                    @forelse($moduleBreakdown as $module)
                        <tr>
                            <td>{{ $module['module'] }}</td>
                            <td>{{ $module['answered'] }}</td>
                            <td>{{ $module['correct'] }}</td>
                            <td>{{ $module['review'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No question-level answers were recorded for this attempt.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <footer class="footer">
            <span>This report is generated from IELTS Master mock test records.</span>
            <span>Report ID: IM-{{ str_pad((string) $attempt->id, 6, '0', STR_PAD_LEFT) }}</span>
        </footer>
    </main>
</body>
</html>
