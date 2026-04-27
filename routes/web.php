<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ClassRecording;
use App\Models\GuidedPracticeVideo;
use App\Http\Controllers\StudentReportController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('instructor')) {
        if ($user->instructor_status !== 'approved') {
            return redirect()->route('instructor.verification.pending');
        }
        return redirect()->route('instructor.dashboard');
    }

    return redirect()->route('student.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/recordings/{recording}/play', function (ClassRecording $recording) {
        abort_unless($recording->recording_path, 404);
        abort_unless(Storage::disk('public')->exists($recording->recording_path), 404);

        return response()->file(Storage::disk('public')->path($recording->recording_path));
    })->name('recordings.play');

    Route::get('/guided-videos/{video}/play', function (GuidedPracticeVideo $video) {
        abort_unless($video->video_path, 404);
        abort_unless(Storage::disk('public')->exists($video->video_path), 404);

        return response()->file(Storage::disk('public')->path($video->video_path));
    })->name('guided_videos.play');

    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', \App\Livewire\Pages\Student\Dashboard::class)->name('student.dashboard');
        Route::get('/student/profile', \App\Livewire\Pages\Student\Profile::class)->name('student.profile');
        Route::get('/student/guided-practice', \App\Livewire\Pages\Student\GuidedPractice::class)->name('student.guided_practice');
        Route::get('/student/vocabulary', \App\Livewire\Pages\Student\Vocabulary::class)->name('student.vocabulary');
        Route::get('/student/smart-practice', \App\Livewire\Pages\Student\SmartPractice::class)->name('student.smart_practice');
        Route::get('/student/bookmarks', \App\Livewire\Pages\Student\BookmarkedQuestions::class)->name('student.bookmarks');
        Route::get('/test/{id}', \App\Livewire\Pages\Student\TestAttempt::class)->name('student.test.attempt');
        Route::get('/student/history', \App\Livewire\Pages\Student\TestHistory::class)->name('student.history');
        Route::get('/student/reports/{attempt}/band-score', [StudentReportController::class, 'show'])->name('student.reports.band_score');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', \App\Livewire\Pages\Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/profile', \App\Livewire\Pages\Admin\Profile::class)->name('admin.profile');
        Route::get('/admin/guided-practice', \App\Livewire\Pages\Instructor\GuidedPractice::class)->name('admin.guided_practice');
        Route::get('/admin/users', \App\Livewire\Pages\Admin\UserManagement::class)->name('admin.users');
        Route::get('/admin/students', \App\Livewire\Pages\Admin\Students::class)->name('admin.students');
        Route::get('/admin/instructors', \App\Livewire\Pages\Admin\Instructors::class)->name('admin.instructors');
        Route::get('/admin/question-bank', \App\Livewire\Pages\Admin\QuestionBank\Index::class)->name('admin.question_bank');
        Route::get('/admin/question-bank/create', \App\Livewire\Pages\Admin\QuestionBank\Edit::class)->name('admin.question_bank.create');
        Route::get('/admin/question-bank/{item}/edit', \App\Livewire\Pages\Admin\QuestionBank\Edit::class)->name('admin.question_bank.edit');
        Route::get('/admin/mock-tests', \App\Livewire\Pages\Admin\MockTest\Index::class)->name('admin.mock_test');
        Route::get('/admin/mock-tests/create', \App\Livewire\Pages\Admin\MockTest\Edit::class)->name('admin.mock_test.create');
        Route::get('/admin/mock-tests/{mockTest}/edit', \App\Livewire\Pages\Admin\MockTest\Edit::class)->name('admin.mock_test.edit');
        Route::get('/admin/instructor-verification', \App\Livewire\Pages\Instructor\VerificationPanel::class)->name('admin.instructor.verification');
    });

    Route::middleware('role:instructor')->group(function () {
        Route::get('/instructor/dashboard', \App\Livewire\Pages\Instructor\Dashboard::class)->name('instructor.dashboard');
        Route::get('/instructor/verification-pending', \App\Livewire\Pages\Instructor\VerificationPending::class)->name('instructor.verification.pending');
        Route::get('/instructor/profile', \App\Livewire\Pages\Instructor\Profile::class)->name('instructor.profile');
        Route::get('/instructor/guided-practice', \App\Livewire\Pages\Instructor\GuidedPractice::class)->name('instructor.guided_practice');
        Route::get('/instructor/question-bank', \App\Livewire\Pages\Admin\QuestionBank\Index::class)->name('instructor.question_bank');
        Route::get('/instructor/question-bank/create', \App\Livewire\Pages\Admin\QuestionBank\Edit::class)->name('instructor.question_bank.create');
        Route::get('/instructor/question-bank/{item}/edit', \App\Livewire\Pages\Admin\QuestionBank\Edit::class)->name('instructor.question_bank.edit');
        Route::get('/instructor/mock-tests', \App\Livewire\Pages\Admin\MockTest\Index::class)->name('instructor.mock_test');
        Route::get('/instructor/mock-tests/create', \App\Livewire\Pages\Admin\MockTest\Edit::class)->name('instructor.mock_test.create');
        Route::get('/instructor/mock-tests/{mockTest}/edit', \App\Livewire\Pages\Admin\MockTest\Edit::class)->name('instructor.mock_test.edit');
    });
});
