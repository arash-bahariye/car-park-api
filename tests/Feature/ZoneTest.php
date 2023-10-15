<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\ResponseTrait;
use Tests\TestCase;

class ZoneTest extends TestCase
{

    use RefreshDatabase;

    public function testPublicUserCanGetAllZones(): void
    {
        $response = $this->getJson('/api/v1/zones');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(3,'data')
            ->assertJsonPath('data.0.name','Green zone');
    }
}
