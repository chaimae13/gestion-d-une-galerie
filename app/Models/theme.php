<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class theme extends Model
{
    protected $fillable = [
        'nom', 'descreption',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
