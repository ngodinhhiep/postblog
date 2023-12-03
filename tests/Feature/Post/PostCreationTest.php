<?php

namespace Tests\Feature\Post;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostCreationTest extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testAuthenticatedUserCanViewPostForm()
    {
        $authenticatedUser = User::inRandomOrder()->first();

        $response = $this->actingAs($authenticatedUser)->get("create-post");;
        
        $response->assertViewIs('create-post');
    }

    public function testUnauthenticatedUserCannotViewPostForm() {

        $response = $this->get("create-post");

        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanCreatePost() {

        $authenticatedUser = User::inRandomOrder()->first();

        $postData = [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];

        $response = $this->actingAs($authenticatedUser)->post('create-post', [
            'title' => $postData['title'],
            'body' => $postData['body'],
        ]);

        $this->assertDatabaseHas('posts', ['title' => $postData['title'], 'body' => $postData['body'], 'user_id' => $authenticatedUser->id]);

        $latestCreatedPost = Post::latest('id')->first();

        $response->assertRedirect("/post/{$latestCreatedPost->id}");
        $response->assertSessionHas('success', 'A new post has been created');

    }
}
