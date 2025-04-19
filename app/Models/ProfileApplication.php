<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileApplication extends Model
{
    protected $fillable = [
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
