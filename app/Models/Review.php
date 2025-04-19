<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'title',
        'content',
        'rating',
    ];
    
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'reviewed_id');
    }
}
