<?php

namespace Tests\Feature;

use App\Livewire\Pages\Student\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('student');
    }

    public function test_student_can_update_profile_fields(): void
    {
        $user = User::factory()->create([
            'timezone' => 'UTC',
        ]);
        $user->assignRole('student');

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', 'Updated Student')
            ->set('phone', '01700000000')
            ->set('country', 'Bangladesh')
            ->set('timezone', 'Asia/Dhaka')
            ->set('exam_type', 'academic')
            ->set('study_goal', 'Need IELTS for university admission')
            ->set('daily_study_minutes', 120)
            ->set('bio', 'Focused on writing and speaking practice')
            ->set('target_band', 7.5)
            ->set('preferred_test_date', now()->addDays(30)->toDateString())
            ->call('updateProfile')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Updated Student', $user->name);
        $this->assertSame('01700000000', $user->phone);
        $this->assertSame('Bangladesh', $user->country);
        $this->assertSame('Asia/Dhaka', $user->timezone);
        $this->assertSame('academic', $user->exam_type);
        $this->assertSame('Need IELTS for university admission', $user->study_goal);
        $this->assertSame(120, $user->daily_study_minutes);
        $this->assertSame('Focused on writing and speaking practice', $user->bio);
        $this->assertSame('7.5', (string) $user->target_band);
    }

    public function test_target_band_must_use_half_band_steps(): void
    {
        $user = User::factory()->create([
            'timezone' => 'UTC',
        ]);
        $user->assignRole('student');

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('target_band', 7.3)
            ->call('updateProfile')
            ->assertHasErrors(['target_band']);
    }

    public function test_student_can_upload_and_remove_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'timezone' => 'UTC',
        ]);
        $user->assignRole('student');

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('profile_photo', UploadedFile::fake()->image('avatar.jpg'))
            ->call('updateProfile')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->profile_photo);
        Storage::disk('public')->assertExists($user->profile_photo);

        Livewire::test(Profile::class)
            ->call('removePhoto')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertNull($user->profile_photo);
    }

    public function test_student_can_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123'),
            'timezone' => 'UTC',
        ]);
        $user->assignRole('student');

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('current_password', 'OldPassword123')
            ->set('new_password', 'NewPassword123')
            ->set('new_password_confirmation', 'NewPassword123')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $user->password));
    }
}
