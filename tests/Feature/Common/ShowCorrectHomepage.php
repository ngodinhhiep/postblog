<?php

namespace Tests\Feature\Common;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowCorrectHomepage extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testHomepageFeedForAuthenticatedUser()
    {
        $authenticatedUser = User::inRandomOrder()->first();

        $response = $this->actingAs($authenticatedUser)->get('/');

        $response->assertViewIs('homepage-feed');
        $response->assertViewHas('posts');
    }

    public function testHomepageFeedForUnauthenticatedUser()
    {

        $response = $this->get('/');

        $response->assertViewIs('homepage');
        $response->assertViewHas('postCount');
    }
}
