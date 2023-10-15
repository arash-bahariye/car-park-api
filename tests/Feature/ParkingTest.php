<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanStartParking(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();
        
        $response = $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id' => $zone->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time' => now()->toDateTimeString(),
                    'stop_time' => null,
                    'total_price' => 0
                ]
            ]);
        $this->assertDatabaseCount('parkings', '1');
    }

        public function testUserCanStoptParking(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();
        
        $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id' => $zone->id,
        ]);

        $this->travel(2)->hours();
        $parking = Parking::first();

        $response = $this->actingAs($user)->putJson('/api/v1/parkings/'.$parking->id);
        $updatedParking = Parking::find($parking->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time' => $updatedParking->start_time->toDateTimeString(),
                    'stop_time' => $updatedParking->stop_time->toDateTimeString(),
                    'total_price' => $updatedParking->total_price
                ]
            ]);
        $this->assertDatabaseCount('parkings', '1');
    }
}
