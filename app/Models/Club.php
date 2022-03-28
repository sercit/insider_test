<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    public function tournaments(){
        return $this->belongsToMany(Tournament::class, 'club_tournament');
    }

    public function tournament(){
        return $this->tournaments()->latest();
    }

    public function homeMatches(){
        return $this->hasMany(Match::class, 'home_club_id');
    }

    public function awayMatches(){
        return $this->hasMany(Match::class, 'away_club_id');
    }

    public function getMatchesAttribute($value)
    {
        return $this->homeMatches->merge($this->awayMatches);
    }
}
