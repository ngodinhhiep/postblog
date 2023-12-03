<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function postDetails(Post $post) {
        
        return response()->json([
            'body' => $post->body
        ]);
    }

    public function adminPostManage() {
        $posts = Post::all();
        return view('adminPostManage', ['posts' => $posts]);
    }

    public function adminPostDelete(Post $post) {
        $post->delete();
        return redirect('/admin/posts')->with('success', 'post successfully deleted!');
    }

    public function adminPostEdit(Request $request, Post $post) {
        $post->title = $request['post_title'];
        $post->body = $request['post_body'];

        return $post->save();
    }

    public function adminUserManage() {
        $users = User::all();
        return view('adminUserManage', ['users' => $users]);
    }

    public function adminUserDelete(User $user) {
        $user->delete();
        return redirect('/admin/users')->with('success', 'User successfully deleted!');
    }

    public function adminUserEdit(User $user, Request $request) {
        $user->username = $request['username'];

        return $user->save();
    }
}
