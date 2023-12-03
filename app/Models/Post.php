<?php

namespace App\Models;

use App\Models\User;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use Searchable;
    use HasFactory;

    protected $fillable = ['title', 'body', 'user_id'];

    public function toSearchableArray() // this name needs to be exact like this
    {
        return [
            'title'=> $this->title,
            'body'=> $this->body
        ];
    }

    // defining the relationship between post and user
    public function user() {
        // dd($this->belongsTo(User::class));
        return $this->belongsTo(User::class); // second argument is the column name that the relationship is powered by
    }
}
    