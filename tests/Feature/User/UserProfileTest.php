<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
   

    // Base test method for shared data
    private function assertSharedDataOnRouteForUser($route, $viewName, $userToVisit, $loggedInUser)
    {
        $this->actingAs($loggedInUser);
        $response = $this->get($route);

        // Assert that the correct view is returned
        $response->assertViewIs($viewName);

        // Assert sharedData contains correct values
        $response->assertViewHas('sharedData.followingExist', Follow::where([['user_id', '=', $loggedInUser->id], ['followedUser', '=', $userToVisit->id]])->count());
        $response->assertViewHas('sharedData.postCount', $userToVisit->posts()->count());
        $response->assertViewHas('sharedData.followerCount', $userToVisit->followers()->count());
        $response->assertViewHas('sharedData.followingCount', $userToVisit->followedUser()->count());
    }

    public function testProfileSharedData()
    {
        $user1 = User::inRandomOrder()->first(); // loggedInUser
        $user2 = User::inRandomOrder()->first(); // userToVisit
        Follow::create(['user_id' => $user1->id, 'followedUser' => $user2->id]);

        // $response = $this->actingAs($user1)->get("/profile/{$user2->username}");
        // $response->assertViewIs('profile');
        // $response->assertViewHas('posts', $user2->posts()->get());

       
        $this->assertSharedDataOnRouteForUser("/profile/{$user2->username}", 'profile', $user2, $user1);
    }

    public function testProfileFollowersSharedData()
    {
        $user1 = User::inRandomOrder()->first();
        $user2 = User::inRandomOrder()->first();
        Follow::create(['user_id' => $user1->id, 'followedUser' => $user2->id]);

        // $response = $this->actingAs($user1)->get("/profile/{$user2->username}/followers");
        // $response->assertViewIs('profile-followers');
        // $response->assertViewHas('followers', $user2->followers()->get());

        $this->assertSharedDataOnRouteForUser("/profile/{$user2->username}/followers", 'profile-followers', $user2, $user1);
    }

    public function testProfileFollowingSharedData()
    {
        $user1 = User::inRandomOrder()->first();
        $user2 = User::inRandomOrder()->first();
        Follow::create(['user_id' => $user1->id, 'followedUser' => $user2->id]);

         // $response = $this->actingAs($user1)->get("/profile/{$user2->username}/following");
        // $response->assertViewIs('profile-following');
        // $response->assertViewHas('following', $user2->followedUser()->get());

        $this->assertSharedDataOnRouteForUser("/profile/{$user2->username}/following", 'profile-following', $user2, $user1);
    }

    public function testAvatarFormWithAuthentication() {
        $loggedInUser = User::inRandomOrder()->first();

        // if Authenticated
        $response = $this->actingAs($loggedInUser)->get('/manage-avatar');
        $response->assertViewIs('avatar-form');
    }

    public function testAvatarFormAccessWithoutAuthentication()
    {
        $response = $this->get('/manage-avatar');
        $response->assertRedirect('/');
    }   

    // public function testUserCanUploadAvatar() {
    //     // Create a test user
    //     $user = User::factory()->create();
    //     $file = UploadedFile::fake()->image('storm.jpg');
    
    //     // Fake the 'public' disk so we don't actually store the uploaded image
    //     Storage::fake('public');
    
    //     // Act as the created user and hit the manage-avatar endpoint
    //     $response = $this->actingAs($user)->post('manage-avatar', ['avatar' => $file]);
    
    
    //     // Fetch the updated user from the database to get the new avatar filename
    //     $updatedUser = User::find($user->id);
    
    //     // Ensure that the user's avatar field is updated in the database
    //     $this->assertNotNull($updatedUser->avatar);

    //     // Assert the uploaded file was stored on the fake public disk in the avatars directory
    //     Storage::disk('public')->assertExists('avatars/' . $updatedUser->avatar);
    
    //     // Check if the image was resized correctly
    //     // First, get the image contents from the fake public disk
    //     $imageContent = Storage::disk('public')->get('avatars/' . $updatedUser->avatar);
    //     $img = Image::make($imageContent);
    //     $this->assertEquals(120, $img->width());
    //     $this->assertEquals(120, $img->height());
    // }
    
    
}




