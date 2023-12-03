<?php

namespace Tests\Feature\Post;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostUpdationTest extends TestCase
{
    
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testUserCanUpdateTheirOwnPost() {

        $postOwner = User::has('posts')->inRandomOrder()->first();

        $post = $postOwner->posts->random();

        // Define the new values for the post
        $updatedData = [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];

        $response = $this->actingAs($postOwner)->put("/post/{$post->id}", $updatedData);

        $response->assertStatus(302); // assert redirection status
        $response->assertRedirect("post/{$post->id}");
        $response->assertSessionHas('success', 'you have successfully updated your post!');

         // Ensure the post in the database has the updated values
         $post = $post->fresh(); // will just reload the db with changed values, 'user_id' remains unchanged
         $this->assertEquals($post->title, $updatedData['title']);
         $this->assertEquals($post->body, $updatedData['body']);
    }

    public function testUserCannotUpdateOtherUsersPost() {
        $postOwner = User::has('posts')->inRandomOrder()->first(); 
        $anotherUser = User::where('id', '!=', $postOwner->id)->inRandomOrder()->first();

        $postToUpdate = $postOwner->posts->random();

        $updatedData = [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph()
        ];

        $response = $this->actingAs($anotherUser)->put("/post/{$postToUpdate->id}", $updatedData);

        $response ->assertStatus(403);

        // assert db has not changed
        $this->assertDatabaseHas('posts', [
            'id' => $postToUpdate->id,
            'title' => $postToUpdate->title,
            'body' => $postToUpdate->body
        ]);
    }
}
