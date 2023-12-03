<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Jobs\SendEmailConfirmation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{

    

    public function register(Request $request)
    {
        $incomingFields = $request->validate([ // if the request doesnt meet the validation, it wont redirect to the new route
            'username' => ['required', 'min:3', 'max:15', Rule::unique('users', 'username')],
            'email' => ['required', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'confirmed'],
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        
        dispatch(new SendEmailConfirmation(['sendTo' => $incomingFields['email'], 'name' => $incomingFields['username']]));

        $user = User::create($incomingFields);
        auth()->login($user);

        return redirect('/')->with('success', 'Thank you for creating an account.');
    }


    public function signin(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            return redirect('/');
        } else {
            return redirect('/')->with('failure', 'Please try again, either the username or password is incorrect');
        }
    }

    public function signinApi(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(auth()->attempt($incomingFields)) {
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('apptoken')->plainTextToken;
            return response()->json(['token' => $token]);
        } else {
            return 'please try again';
        }
    }

    // show the correct homepage according to the user who has logged in or not
    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(4)]);
        } else {
            if(Cache::has('postCount')) {
                $postCount = Cache::get('postCount');
            } else {
                $postCount = Post::count();
                Cache::put('postCount', $postCount, 50);
            }
            return view('homepage', ['postCount' => $postCount]);
        }
    }


    public function signout()
    {
        auth()->logout();
        return redirect('/');
    }


    private function getSharedData($user) // user to visit
    {
        // you cannot follow someone you are already following (if the follow exists, you cannot follow them anymore)
        $followingExist = Follow::where([['user_id', '=', auth()->user()->id], ['followedUser', '=', $user->id]])->count();

        View::share('sharedData', ['user' => $user, 'followingExist' => $followingExist, 'postCount' => $user->posts()->count(),
                                    'followerCount' => $user->followers()->count(), 'followingCount' => $user->followedUser()->count()]);
    }

    public function profile(User $user) // user to visit
    {
        if (auth()->check()) {
            $this->getSharedData($user);
            return view('profile', ['posts' => $user->posts()->latest()->get(), 'user' => $user]);
        }
    }


    public function profileFollowers(User $user)
    {
        if (auth()->check()) {
            $this->getSharedData($user);
            return view('profile-followers', ['followers' => $user->followers()->latest()->get()]);
        }
    }


    public function profileFollowing(User $user)
    {
        if (auth()->check()) {
            $this->getSharedData($user);
            return view('profile-following', ['following' => $user->followedUser()->latest()->get()]);
        }
    }


    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    
    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|image|max:3000'
        ]);

        $user = auth()->user();
        $fileName = $user->id . '_' . uniqid() . '.jpg';

        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg'); // resize the raw uploaded image 

        Storage::disk('public')->put('avatars/' . $fileName, $imgData);

        $user->avatar = $fileName;
        $user->save();

        return redirect('/');
    }
}
