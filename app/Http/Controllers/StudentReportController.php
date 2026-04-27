<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class StudentReportController extends Controller
{
    public function show(TestAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        abort_unless($attempt->status === 'completed' && $attempt->overall_band !== null, 404);

        $attempt->load([
            'user',
            'mockTest',
            'answers.question.group.item',
        ]);

        $autoGradedAnswers = $attempt->answers
            ->filter(fn ($answer) => $answer->is_correct !== null);

        $correctAnswers = $autoGradedAnswers
            ->filter(fn ($answer) => $answer->is_correct)
            ->count();

        $moduleBreakdown = $attempt->answers
            ->filter(fn ($answer) => $answer->question?->group?->item)
            ->groupBy(fn ($answer) => $answer->question->group->item->module)
            ->map(fn ($answers, string $module) => [
                'module' => ucfirst($module),
                'answered' => $answers->count(),
                'correct' => $answers->where('is_correct', true)->count(),
                'review' => $answers->where('is_correct', false)->count(),
            ])
            ->values();

        $data = [
            'attempt' => $attempt,
            'autoGradedTotal' => $autoGradedAnswers->count(),
            'correctAnswers' => $correctAnswers,
            'moduleBreakdown' => $moduleBreakdown,
            'isPdf' => true,
        ];

        $fileName = 'ielts-band-score-report-' . $attempt->id . '.pdf';

        return Pdf::loadView('reports.student-band-score', $data)
            ->setPaper('a4')
            ->download($fileName);
    }
}
