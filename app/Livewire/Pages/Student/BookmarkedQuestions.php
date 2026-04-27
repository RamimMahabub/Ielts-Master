<?php

namespace App\Livewire\Pages\Student;

use App\Models\StudentQuestionBookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class BookmarkedQuestions extends Component
{
    public $bookmarks;
    public bool $bookmarksReady = true;

    public function mount(): void
    {
        $this->loadBookmarks();
    }

    public function removeBookmark(int $questionId): void
    {
        if (!$this->bookmarksTableExists()) {
            $this->bookmarksReady = false;
            return;
        }

        StudentQuestionBookmark::where('user_id', Auth::id())
            ->where('question_id', $questionId)
            ->delete();

        $this->loadBookmarks();
    }

    private function loadBookmarks(): void
    {
        if (!$this->bookmarksTableExists()) {
            $this->bookmarksReady = false;
            $this->bookmarks = collect();
            return;
        }

        $this->bookmarksReady = true;
        $this->bookmarks = StudentQuestionBookmark::where('user_id', Auth::id())
            ->with('question.group.item')
            ->latest()
            ->get();
    }

    private function bookmarksTableExists(): bool
    {
        return Schema::hasTable('student_question_bookmarks');
    }

    public function render()
    {
        return view('livewire.pages.student.bookmarked-questions')->layout('layouts.app');
    }
}
