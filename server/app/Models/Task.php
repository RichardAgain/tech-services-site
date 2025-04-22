<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function requested()
    {
        return $this->belongsTo(User::class, 'requested_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
