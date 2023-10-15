<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    
    use RefreshDatabase;

    public function testUserCanGetTheirOwnVehicle(): void
    {
        $userOne = User::factory()->create();
        $vehicleFromUserOne = Vehicle::factory()->create([
            'user_id' => $userOne->id
        ]);

        $userTwo = User::factory()->create();
        $vehicleFromUserTwo = Vehicle::factory()->create([
            'user_id' => $userTwo->id
        ]);

        $response = $this->actingAs($userOne)->getJson('/api/v1/vehicles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, ['data'])
            ->assertJsonPath('data.0.plate_number', $vehicleFromUserOne->plate_number)
            ->assertJsonMissing($vehicleFromUserTwo->toArray());
    }

    public function testUserCanCreateVehicle(){
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/vehicles', [
            'plate_number' => '123456'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, ['data'])
            ->assertJsonStructure([
                'data' => ['0' => 'plate_number'],
            ]);
            
            $this->assertDatabaseHas('vehicles', [
                'plate_number' => '123456'
            ]);
    }

    public function testUserCanUpdateTheirVehicle(){
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/'.$vehicle->id, [
            'plate_number' =>'654321'
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['plate_number'])
            ->assertJsonPath('plate_number', '654321');
        
        $this->assertDatabaseHas('vehicles', [
            'plate_number' =>'654321'
        ]);
    }

    public function testUserCanDeleteTheirVehicle(){
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/'.$vehicle->id);
        $response->assertStatus(422);
    }
}
