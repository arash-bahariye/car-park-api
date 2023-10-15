<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLoginWithCorrectCredentials(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/auth/login',[
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(201);
    }

    public function testUserCanNotLoginWithIncorrectCredentials(): void{
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/auth/login',[
            'email' => $user->email,
            'password' => 'incorrect_password'
        ]);

        $response->assertStatus(422);
    }

    public function testUserCanRegisterWithCorrectCredentials(){
        $response = $this->postJson('/api/v1/auth/register',[
            'name' => 'john',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'access_token'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'john',
            'email' => 'john@example.com'
        ]);
    }

        public function testUserCanNotRegisterWithCorrectCredentials(){
        $response = $this->postJson('/api/v1/auth/register',[
            'name' => 'john',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'incorrect_password'
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
            'name' => 'john',
            'email' => 'john@example.com'
        ]);
    }
}
