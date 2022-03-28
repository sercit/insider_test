<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }
}
