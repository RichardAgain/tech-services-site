<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileApplication extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
