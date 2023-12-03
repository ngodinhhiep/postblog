<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;


class PostController extends Controller
{
    public function showCreateForm() {
        return view('create-post');
    }


    public function storeNewPost(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);
       
        return redirect("/post/{$newPost->id}")->with('success', 'A new post has been created');
    }

    public function storeNewPostApi(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);
       
        return response()->json(['id' => $newPost->id]);
    }

    public function viewSinglePost(Post $post) {
        return view('single-post', ['post' => $post]);
    }

    public function deletePost(Post $post) {
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'you have deleted the post');
    }

    public function deletePostApi(Post $post) {
        $post->delete();
        return 'post deleted';
    }

    public function showEditForm(Post $post) {
        return view('edit-post', ['post' => $post]);
    }


    public function editPost(Post $post, Request $request) {
        $incomingFields = $request->validate([
            'title' => 'nullable',
            'body' => 'nullable'
        ]);


        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);

        return redirect("/post/{$post->id}")->with('success', 'you have successfully updated your post!');
    }

    public function searchBox($searchTerms) { 
        $posts = Post::search($searchTerms)->get();  
        $posts->load('user:id,username,avatar');

        return $posts; // return all the related posts and their associated users
    }
}
