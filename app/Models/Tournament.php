<?php

namespace App\Models;

use App\Services\TournamentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    public function clubs(){
        return $this->belongsToMany(Club::class, 'club_tournament');
    }

    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
    }

    public function matches()
    {
        return $this->hasManyThrough(Match::class, Fixture::class);
    }
}
