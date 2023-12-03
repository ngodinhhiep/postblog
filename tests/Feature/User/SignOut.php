<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignOut extends TestCase
{
    use WithFaker;
    
    /**
     * A basic feature test example.
     */
    public function testIfUserCanSignout()
    {
        $user = User::inRandomOrder()->first();

        $response = $this->actingAs($user)->get('/signout');

        $response->assertRedirect('/');
        $this->assertFalse(Auth::check()); // assert that theres no authenticated user (meaning the user has been logged out)
    }
}
