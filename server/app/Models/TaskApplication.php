<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskApplication extends Model
{
    protected $fillable = [
        'requester_id',
        'required_id',
        'title',
        'description',
        'status',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function required()
    {
        return $this->belongsTo(User::class, 'required_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
