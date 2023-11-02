<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class photo extends Model
{
    protected $fillable = [
        'filename', 'path','cloud_url', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
