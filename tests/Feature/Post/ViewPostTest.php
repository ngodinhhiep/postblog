<?php

namespace Tests\Feature\Post;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewPostTest extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testAuthenticatedUserCanViewSinglePost(): void
    {
        $user1 = User::inRandomOrder()->first();
        $user2 = User::where('id', '!=', $user1->id)->has('posts')->inRandomOrder()->first();

       
        $post = $user2->posts->random();

        $response = $this->actingAs($user1)->get("/post/{$post->id}");
        
        $response->assertViewIs('single-post');
        $response->assertViewHas('post', $post);
    }

    public function testUnauthenticatedUserCannotViewSinglePost() {
        $user = User::has('posts')->inRandomOrder()->first();

        $post = $user->posts->random();

        $response = $this->get("/post/{$post->id}");

        $response->assertRedirect("/");
    }

    public function testUserCanSeeEditAndDeleteButtonsForTheirOwnPost()
    {
        $user = User::has('posts')->inRandomOrder()->first();
        $post = $user->posts->random();

        $response = $this->actingAs($user)->get("/post/{$post->id}");

        $response->assertSee('fas fa-edit');
        $response->assertSee('fas fa-trash');
    }

    
    public function testUserCannotSeeEditAndDeleteButtonsForOthersPost()
    {
        $postOwner = User::has('posts')->inRandomOrder()->first();
        $anotherUser = User::inRandomOrder()->first();

        $post = $postOwner->posts->random();

        $response = $this->actingAs($anotherUser)->get("/post/{$post->id}");

        $response->assertDontSee('fas fa-edit');
        $response->assertDontSee('fas fa-trash');
    }
}
