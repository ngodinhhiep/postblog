<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user) {
        // you cannot follow yourself
        if($user->id == auth()->user()->id) {
            return back()->with('failure', 'you cannot follow yourself');
        }

        // you cannot follow someone you are already following
        $followingExist = Follow::where([['user_id', '=', auth()->user()->id],['followedUser', '=', $user->id]])->count();

        if($followingExist) {
            return back()->with('failure', 'you are already following that person');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followedUser = $user->id;

        $newFollow->save();

        return back()->with('success', 'followed successfully');

    }


    public function removeFollow(User $user) {
        Follow::where([['user_id', '=', auth()->user()->id],['followeduser', '=', $user->id]])->delete();

        return back()->with('success', 'Unfollowed successfully');
    }
}

