<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
}
