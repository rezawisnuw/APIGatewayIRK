<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IRKVersionGatewayTest extends TestCase
{
    public function testGetVersion()
    {
        $response = $this->json('POST', '/api/live/version/get', [
            'key' => 'value', 
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testPostVersion()
    {
        $response = $this->json('POST', '/api/live/version/post', [
            'key' => 'value',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }
}
