<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetTheirProfile(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => $user->name, 'email' => $user->email]);
    }

    public function testUserCanUpdateNameAndEmail(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/profile', [
            'name' => 'Updated_name',
            'email' => 'updated@email.com'
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'name' => 'Updated_name',
                'email' => 'updated@email.com'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Updated_name',
            'email' => 'updated@email.com'
        ]);
    }

    public function testUserCanChangePassword(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/password', [
            'current_password' => 'password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        ]);

        $response->assertStatus(202);
    }

    public function testUnauthenticatedProfileAcces(){
        $response = $this->getJson('/api/v1/profile');
        $response->assertStatus(401);
    }
}
