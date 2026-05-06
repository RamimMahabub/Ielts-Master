<?php

namespace Tests\Feature;

use App\Livewire\Auth\Register;
use App\Livewire\Pages\Admin\UserManagement;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Test24241073_admin_user_management extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin');
        Role::findOrCreate('instructor');
        Role::findOrCreate('student');
    }

    public function test_create_user_through_registration_flow(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Assignment Student')
            ->set('email', 'assignment.student@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'student')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $user = User::where('email', 'assignment.student@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('student'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Assignment Student',
            'email' => 'assignment.student@example.com',
            'is_blocked' => false,
            'instructor_status' => 'none',
        ]);
    }

    public function test_admin_can_read_users_from_user_management_page(): void
    {
        $admin = $this->createUserWithRole('admin');
        $student = $this->createUserWithRole('student', [
            'name' => 'Readable Student',
            'email' => 'readable.student@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee($student->name)
            ->assertSee($student->email);
    }

    public function test_admin_can_update_user_profile_role_and_block_status(): void
    {
        $admin = $this->createUserWithRole('admin');
        $student = $this->createUserWithRole('student');

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('editUser', $student->id)
            ->assertSet('editingUserId', $student->id)
            ->set('editName', 'Updated User')
            ->set('editEmail', 'updated.user@example.com')
            ->set('editRole', 'instructor')
            ->set('editIsBlocked', true)
            ->call('updateUser')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $student->refresh();

        $this->assertTrue($student->hasRole('instructor'));
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Updated User',
            'email' => 'updated.user@example.com',
            'is_blocked' => true,
            'instructor_status' => 'approved',
        ]);
    }

    public function test_update_user_validation_fails_when_name_is_missing(): void
    {
        $admin = $this->createUserWithRole('admin');
        $student = $this->createUserWithRole('student');

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('editUser', $student->id)
            ->set('editName', '')
            ->call('updateUser')
            ->assertHasErrors(['editName' => 'required']);

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
        ]);
    }

    public function test_missing_user_returns_not_found_error_for_edit_action(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('editUser', 999999);
    }

    public function test_guest_cannot_access_admin_user_management(): void
    {
        $this->get(route('admin.users'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_delete_non_admin_user(): void
    {
        $admin = $this->createUserWithRole('admin');
        $student = $this->createUserWithRole('student');

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('confirmDelete', $student->id)
            ->assertSet('deletingUserId', $student->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteUser')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('users', [
            'id' => $student->id,
        ]);
    }

    private function createUserWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
