<?php

namespace Tests\Feature\Follow;

use Tests\TestCase;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowCreationTest extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testUserCannotFollowThemselves()
    {
        $user = User::inRandomOrder()->first();

        $response = $this->actingAs($user)->post("/create-follow/{$user->username}"); // auth()->user()->id = $user->id

        $response->assertSessionHas('failure', 'you cannot follow yourself');

        $this->assertDatabaseMissing('follows', [ // assure that theres no row(record) like that exists
            'user_id' => $user->id,
            'followedUser' => $user->id   
        ]);
    }

    public function testUserCannotFollowSomeoneTheyAreAlreadyFollowing() {
        $user = User::has('followedUser')->inRandomOrder()->first(); // get the user who are already following someone, not newly created user
       
        $followedUserRelation = $user->followedUser->random(); // get a random row in Follow table (not all rows)

        $followedUser = $followedUserRelation->userBeingFollowed; // get the user being followed by $user

        $initialCount = $user->followedUser->count(); // count all the Follow rows that have 'user_id' == $user->id

        $response = $this->actingAs($user)->post("create-follow/{$followedUser->username}");

        $response->assertSessionHas('failure', 'you are already following that person');

      
        $this->assertCount($initialCount, $user->fresh()->followedUser); // assert that the count stays the same
    }

    public function testUserCanFollowAnotherUser() {
        $user = User::inRandomOrder()->first();
        $followedUser = User::where('id', '!=', $user->id)->inRandomOrder()->first();

        $initialCount = $user->followedUser->count();

        $response = $this->actingAs($user)->post("create-follow/{$followedUser->username}");

        $response->assertSessionHas('success', 'followed successfully');

        $this->assertCount($initialCount+1, $user->fresh()->followedUser);
    }
}
