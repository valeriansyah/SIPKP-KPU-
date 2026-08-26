<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase7HPelaporProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = Role::firstOrCreate(['role_name' => 'Pelapor']);
        $this->user = User::create([
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'phone_number' => '-',
            'password' => Hash::make('password'),
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);
    }

    public function test_pelapor_can_access_profile()
    {
        $response = $this->actingAs($this->user)->get(route('pelapor.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
    }

    public function test_pelapor_can_update_profile()
    {
        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => 'John Doe Updated',
            'phone_number' => '081234567890',
        ]);

        $response->assertRedirect(route('pelapor.profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'full_name' => 'John Doe Updated',
            'phone_number' => '081234567890',
        ]);
    }

    public function test_pelapor_profile_validation()
    {
        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => '', // invalid
            'phone_number' => '081234567890',
        ]);

        $response->assertSessionHasErrors(['full_name']);

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
            'phone_number' => '081234567890',
        ]);
    }

    public function test_pelapor_can_upload_avatar()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => 'John Doe',
            'phone_number' => '081234567890',
            'profile_picture' => $file,
        ]);

        $response->assertRedirect(route('pelapor.profile.edit'));
        
        $this->user->refresh();
        $this->assertNotNull($this->user->profile_picture);
        Storage::disk('public')->assertExists($this->user->profile_picture);
    }

    public function test_pelapor_can_remove_avatar()
    {
        Storage::fake('public');
        
        // Initial setup: User has a photo
        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->store('profile-photos', 'public');
        
        $this->user->update(['profile_picture' => $path]);
        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => 'John Doe',
            'phone_number' => '081234567890',
            'remove_photo' => 1,
        ]);

        $response->assertRedirect(route('pelapor.profile.edit'));
        
        $this->user->refresh();
        $this->assertNull($this->user->profile_picture);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_pelapor_can_update_password()
    {
        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => 'John Doe',
            'phone_number' => '081234567890',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('pelapor.profile.edit'));
        
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    public function test_pelapor_can_replace_avatar()
    {
        Storage::fake('public');
        
        // Initial setup: User has an old photo
        $oldFile = UploadedFile::fake()->image('old_avatar.jpg');
        $oldPath = $oldFile->store('profile-photos', 'public');
        $this->user->update(['profile_picture' => $oldPath]);
        Storage::disk('public')->assertExists($oldPath);

        // Upload new photo
        $newFile = UploadedFile::fake()->image('new_avatar.png');
        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => 'John Doe',
            'phone_number' => '081234567890',
            'profile_picture' => $newFile,
        ]);

        $response->assertRedirect(route('pelapor.profile.edit'));
        
        $this->user->refresh();
        $this->assertNotNull($this->user->profile_picture);
        $this->assertNotEquals($oldPath, $this->user->profile_picture);
        
        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldPath);
        // New file should exist
        Storage::disk('public')->assertExists($this->user->profile_picture);
    }

    public function test_pelapor_cannot_upload_non_image_avatar()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user)->put(route('pelapor.profile.update'), [
            'full_name' => 'John Doe',
            'phone_number' => '081234567890',
            'profile_picture' => $file,
        ]);

        $response->assertSessionHasErrors(['profile_picture']);
        
        $this->user->refresh();
        $this->assertNull($this->user->profile_picture);
    }
}
