<?php

namespace Tests\Feature\Follow;

use Tests\TestCase;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowDeletiontest extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testUserCanUnfollowTheOneTheyAreFollowing()
    {   
        // $user = User::where('id', 1)->first();
        $user = User::has('followedUser')->inRandomOrder()->first();

        $followedUserRelationship = $user->followedUser->first();

        $followedUser = $followedUserRelationship->userBeingFollowed;

        $response = $this->actingAs($user)->post("/remove-follow/{$followedUser->username}");

        $response->assertSessionHas('success', 'Unfollowed successfully');
        $this->assertDatabaseMissing('follows', [
            'user_id' => $user->id,
            'followedUser' => $followedUser->id
        ]);
    }
}
