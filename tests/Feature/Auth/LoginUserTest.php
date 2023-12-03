<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginUserTest extends TestCase
{  
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testSuccessfulSignin()
    {
        $userRawPassword = 'password123';  // Store it in a variable because we'll bcrypt it for the database and we need to use it in its raw form later.
        $user = User::inRandomOrder()->first();

        $user->password = bcrypt($userRawPassword);

        // Attempt to sign in with the created user's credentials.
        $response = $this->post('/signin',[
            'loginusername' => $user->username,
            'loginpassword' => $userRawPassword
        ]);

        // Assert a successful redirect to the homepage.
        $response->assertRedirect('/');

        $response = $this->actingAs($user)->get('/'); // use actingAs to decalre the authenticated user, because it wont be stored in session
        $response->assertViewIs('homepage-feed');
    }

    public function testSigninFailure() {
         // Get an existing user from the database
         $user = User::inRandomOrder()->first();

        // Attempt to sign in with incorrect password.
        $response = $this->post('/signin', [
            'loginusername' => $user->username,
            'loginpassword' => 'wrongpassword'
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('failure', 'Please try again, either the username or password is incorrect');

        $response = $this->get('/');
        $response->assertViewIs('homepage');
    }
}
