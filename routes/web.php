<?php

use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FollowController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Admin related routes

Route::get('/admin/posts', [AdminController::class, "adminPostManage"])->middleware('can:visitAdminPage');

Route::get('/admin/posts/details/{post}', [AdminController::class, "postDetails"])->middleware('can:visitAdminPage');

Route::post('/admin/posts_edit/{post}', [AdminController::class, "adminPostEdit"]);

Route::post('/admin/posts/delete/{post}', [AdminController::class, "adminPostDelete"])->middleware('can:visitAdminPage');

Route::get('/admin/users', [AdminController::class, "adminUserManage"])->middleware('can:visitAdminPage');

Route::post('/admin/users/delete/{user}', [AdminController::class, "adminUserDelete"])->middleware('can:visitAdminPage');

Route::post('/admin/users_edit/{user}', [AdminController::class, "adminUserEdit"])->middleware('can:visitAdminPage');


// Common
Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login'); //define a route name to login for user who hasnt logged in

Route::post('/register', [UserController::class, "register"])->middleware('guest');

Route::post('/signin', [UserController::class, "signin"])->middleware('guest');


// User related routes
Route::get('/signout', [UserController::class, "signout"])->middleware('mustBeLoggedIn');

Route::get('/manage-avatar', [UserController::class, "showAvatarForm"])->middleware('mustBeLoggedIn');

Route::post('/manage-avatar', [UserController::class, "storeAvatar"])->middleware('mustBeLoggedIn');



// Post related routes
Route::get('/create-post', [PostController::class, "showCreateForm"])->middleware('mustBeLoggedIn');

Route::post('/create-post', [PostController::class, "storeNewPost"])->middleware('mustBeLoggedIn');

Route::get('/post/{post}', [PostController::class, "viewSinglePost"])->middleware('mustBeLoggedIn');

Route::delete('/post/{post}', [PostController::class, "deletePost"])->middleware('can:delete,post');

Route::get('/post/{post}/edit', [PostController::class, "showEditForm"])->middleware('can:update,post');

Route::put('/post/{post}', [PostController::class, "editPost"])->middleware('can:update,post');

Route::get('/search/{searchTerms}', [PostController::class, "searchBox"])->middleware('mustBeLoggedIn');

// Profile related routes
Route::get('/profile/{user:username}', [UserController::class, "profile"])->middleware('mustBeLoggedIn');

Route::get('/profile/{user:username}/followers', [UserController::class, "profileFollowers"])->middleware('mustBeLoggedIn');

Route::get('/profile/{user:username}/following', [UserController::class, "profileFollowing"])->middleware('mustBeLoggedIn');

// Follows related routes
Route::post('/create-follow/{user:username}', [FollowController::class, "createFollow"])->middleware('mustBeLoggedIn');

Route::post('/remove-follow/{user:username}', [FollowController::class, "removeFollow"])->middleware('mustBeLoggedIn');

// Chat route
Route::post('/chat-message', function(Request $request) {
    $chatFields = $request->validate([
        'textValue' => 'required'
    ]);

    if(!trim(strip_tags($chatFields['textValue']))) {
        return response()->noContent(); // return with no json responses
    }

    //broadcast the event
    broadcast(new ChatMessage(['username' => auth()->user()->username, 'textValue' => strip_tags($chatFields['textValue']), 'avatar' => auth()->user()->avatar]))->toOthers();
    return response()->noContent(); // return with no json responses

})->middleware('mustBeLoggedIn');
