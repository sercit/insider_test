<?php

namespace App\Models;

use App\Enum\TournamentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = ['home_club_id', 'away_club_id', 'fixture_id'];

    protected $appends = ['home_points','away_points'];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    public function homeClub()
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub()
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function getHomePointsAttribute($value)
    {
        if($this->home_club_goals > $this->away_club_goals){
            $homePoints = TournamentEnum::POINTS_WIN;
        }elseif($this->home_club_goals === $this->away_club_goals){
            $homePoints = TournamentEnum::POINTS_DRAW;
        }else{
            $homePoints = TournamentEnum::POINTS_LOSS;
        }
        $this->attributes['home_points'] = $homePoints;
        return $homePoints;
    }

    public function getAwayPointsAttribute($value)
    {
        if($this->home_club_goals < $this->away_club_goals){
            $awayPoints = TournamentEnum::POINTS_WIN;
        }elseif($this->home_club_goals === $this->away_club_goals){
            $awayPoints = TournamentEnum::POINTS_DRAW;
        }else{
            $awayPoints = TournamentEnum::POINTS_LOSS;
        }
        $this->attributes['away_points'] = $awayPoints;
        return $awayPoints;
    }
}
