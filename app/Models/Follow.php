<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'followedUser'
    ];

   public function userDoingTheFollowing() {
    return $this->belongsTo(User::class, 'user_id');
   }

   public function userBeingFollowed() {
    return $this->belongsTo(User::class, 'followedUser');
   }
}
