<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', \App\Livewire\Pages\Student\Dashboard::class)->name('student.dashboard');
        Route::get('/student/profile', \App\Livewire\Pages\Student\Profile::class)->name('student.profile');
        Route::get('/test/{id}', \App\Livewire\Pages\Student\TestAttempt::class)->name('student.test.attempt');
        Route::get('/student/history', \App\Livewire\Pages\Student\TestHistory::class)->name('student.history');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', \App\Livewire\Pages\Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/users', \App\Livewire\Pages\Admin\UserManagement::class)->name('admin.users');
        Route::get('/admin/mock-tests/create', \App\Livewire\Pages\Admin\MockTestBuilder::class)->name('admin.mock_test.create');
        Route::get('/admin/question-bank', \App\Livewire\Pages\Admin\QuestionBankList::class)->name('admin.question_bank');
        Route::get('/admin/instructor-verification', \App\Livewire\Pages\Instructor\VerificationPanel::class)->name('admin.instructor.verification');
    });

    Route::middleware('role:instructor')->group(function () {
        Route::get('/instructor/dashboard', \App\Livewire\Pages\Instructor\Dashboard::class)->name('instructor.dashboard');
        Route::get('/instructor/verification-pending', \App\Livewire\Pages\Instructor\VerificationPending::class)->name('instructor.verification.pending');
        Route::get('/instructor/question-bank', \App\Livewire\Pages\Admin\QuestionBankList::class)->name('instructor.question_bank');
        Route::get('/instructor/mock-tests/create', \App\Livewire\Pages\Admin\MockTestBuilder::class)->name('instructor.mock_test.create');
    });
});
