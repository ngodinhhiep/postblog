<?php

namespace Tests\Feature\Post;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostSearchingTest extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testSearchReturnsCorrectResults()
    {
        $user = User::inRandomOrder()->first();

        $randomPost = Post::inRandomOrder()->first();

        $searchTerm = substr($randomPost->title, 0 ,5);

          // Get all the posts containing the term $searchTerm
        $matchingPosts = Post::where('title', 'LIKE', "%{$searchTerm}%")
        ->orWhere('body', 'LIKE', "%{$searchTerm}%")
        ->with('user:id,username,avatar')
        ->limit(20)
        ->get();
                                                    
        // Act as the authenticated user
        $response = $this->actingAs($user)->get("/search/{$searchTerm}");

        foreach($matchingPosts as $matchingPost) {
            $response->assertJsonFragment([
                'title' => $matchingPost->title,
                'body' => $matchingPost->body,
                'user' => [ // name of the loaded method that defines the relationship in the Post model
                    'id' => $matchingPost->user->id,
                    'username' => $matchingPost->user->username,
                    'avatar' => $matchingPost->user->avatar
                ]
            ]);
        }
        $response->assertJsonStructure([
            '*' => ['title', 'body', 'user' => ['id', 'username', 'avatar']]
        ]);
    }

    public function testSearchRequiresAuthentication()
    {
        // Test that the middleware is functioning
        $response = $this->get('/search/test');

        
        $response->assertStatus(302);
        $response->assertRedirect('/'); 
        $response->assertSessionHas('failure', 'you must be logged in');
    }
}
