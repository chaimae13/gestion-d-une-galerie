<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\Output\Theme;

class photo extends Model
{
    protected $fillable = [
        'filename', 'path','cloud_url', 'user_id','theme_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

}
