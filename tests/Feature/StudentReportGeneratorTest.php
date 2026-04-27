<?php

namespace Tests\Feature;

use App\Models\MockTest;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentReportGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('student');
    }

    public function test_student_can_download_pdf_report_for_completed_attempt(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $mockTest = MockTest::create([
            'title' => 'Academic Mock Report',
            'test_type' => 'academic',
            'is_published' => true,
        ]);

        $attempt = TestAttempt::create([
            'user_id' => $student->id,
            'mock_test_id' => $mockTest->id,
            'status' => 'completed',
            'current_module' => 'speaking',
            'listening_band' => 7.0,
            'reading_band' => 7.5,
            'writing_band' => 6.5,
            'speaking_band' => 7.0,
            'overall_band' => 7.0,
            'completed_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.reports.band_score', $attempt))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_student_cannot_download_another_students_report(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $otherStudent = User::factory()->create();
        $otherStudent->assignRole('student');

        $mockTest = MockTest::create([
            'title' => 'Academic Mock Report',
            'test_type' => 'academic',
            'is_published' => true,
        ]);

        $attempt = TestAttempt::create([
            'user_id' => $otherStudent->id,
            'mock_test_id' => $mockTest->id,
            'status' => 'completed',
            'current_module' => 'speaking',
            'overall_band' => 7.0,
            'completed_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.reports.band_score', $attempt))
            ->assertForbidden();
    }
}
