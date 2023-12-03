<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Post;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'nickname'
    ];

    protected function avatar(): Attribute { // the name of the method should match the name of the column on the table
        return Attribute::make(get: function($value) {
            return $value ? '/storage/avatars/' . $value : '/fallback-avatar.jpg';
        });
    }

    public function feedPosts() {
        return $this->hasManyThrough(Post::class, Follow::class, 'user_id', 'user_id', 'id', 'followedUser');
    }

    public function followers() {
        return $this->hasMany(Follow::class, 'followedUser');
    }

    public function followedUser() {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function posts() {
        return $this->hasMany(Post::class); 
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
