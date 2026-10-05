<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_lock_screen_is_accessible(): void
    {
        $response = $this->get('/lock-screen');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_pin_redirects_to_lock_screen(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/lock-screen');
    }

    public function test_authenticated_pin_allows_access_to_dashboard(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])->get('/');

        $response->assertStatus(200);
    }
}

