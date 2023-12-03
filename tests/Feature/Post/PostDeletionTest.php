<?php

namespace Tests\Feature\Post;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostDeletionTest extends TestCase
{
 
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testUserCanDeleteTheirOwnPost()
    {
        $postOwner = User::inRandomOrder()->first(); // authenticated user

        $postToDelete = $postOwner->posts->random();

        $response = $this->actingAs($postOwner)->delete("/post/{$postToDelete->id}");

        $response->assertRedirect("/profile/" . $postOwner->username);
        $response->assertSessionHas('success', 'you have deleted the post');

        $this->assertDatabaseMissing('posts', ['id' => $postToDelete->id]); // assert that the post has been delted
    }

    public function testUserCannotDeleteOtherPeoplesPost() {
        $postOwner = User::inRandomOrder()->first();
        $anotherUser = User::where('id', '!=', $postOwner->id)->inRandomOrder()->first();
       
        $post = Post::factory()->create([
            'user_id' => $postOwner->id
        ]);

        $response = $this->actingAs($anotherUser)->delete("/post/{$post->id}");

        $response->assertStatus(403); // assert status as forbidden

        $this->assertDatabaseHas('posts', ['id' => $post->id]); // assert the post is still in the db(not deleted)

    }

}
