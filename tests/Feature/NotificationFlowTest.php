<?php

namespace Tests\Feature;

use App\Livewire\Pages\Admin\MockTest\Index;
use App\Models\MockTest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('student');
    }

    public function test_students_are_notified_when_mock_test_is_published(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $mockTest = MockTest::create([
            'title' => 'Academic Mock 2',
            'test_type' => 'academic',
            'is_published' => false,
        ]);

        (new Index())->togglePublish($mockTest->id);

        $notification = $student->fresh()->unreadNotifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('new_mock_test', $notification->data['type']);
        $this->assertSame($mockTest->id, $notification->data['mock_test_id']);
    }

    public function test_students_are_not_notified_when_mock_test_is_unpublished(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $mockTest = MockTest::create([
            'title' => 'Academic Mock 2',
            'test_type' => 'academic',
            'is_published' => true,
        ]);

        (new Index())->togglePublish($mockTest->id);

        $this->assertSame(0, $student->fresh()->unreadNotifications()->count());
    }
}
