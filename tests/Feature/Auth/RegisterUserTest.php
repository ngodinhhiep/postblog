<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterUserTest extends TestCase
{
    // use RefreshDatabase; 
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testSuccessfulRegistrationWithValidData()
    {
        $userData = [
            'username' => $this->faker->userName,
            'email' => $this->faker->email,
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123'
        ];

        $response = $this->post('/register', $userData);
        
        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => $userData['email']]); // assert that the user has been created 
        $response->assertSessionHas('success', 'Thank you for creating an account.');
    }


    public function testRegistrationWithDuplicateEmailAndUsername()
    {
        // Fetch an existing user from the database
        $existingUser = User::inRandomOrder()->first();

        $userData = [
            'username' => $existingUser->username,
            'email' => $existingUser->email,
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123'
        ];

        $response = $this->post('/register', $userData);
        
        $response->assertSessionHasErrors(['email', 'username']);
    }

    public function testRegistrationWithShortUsername() {
        $userData = [
            'username' => 'ab',
            'email' => $this->faker->email,
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123'
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['username']);
    }

    public function testRegistrationWithWeakPassword() {
        $userData = [
            'username' => $this->faker->username,
            'email' => $this->faker->email,
            'password' => 'Weak',
            'password_confirmation' => 'Weak'
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['password']);
    }


    public function testPasswordMismatched() {

        $userData = [
            'username' => $this->faker->username,
            'email' => $this->faker->email,
            'password' => 'StrongPass@123',
            'password_confirmation' => 'MismatchedPassword@123'
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['password']);
    }
    
}
