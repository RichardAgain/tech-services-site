<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $table = 'reviews';

    protected $fillable = [
        'reviewer_id',
        'reviewed_id',
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
