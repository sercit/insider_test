<?php

namespace App\Services;

use App\Enum\TournamentEnum;
use App\Models\Match;

class MatchService
{
    public function create(array $data): Match
    {
        return Match::create($data);
    }

    public function generateResult(int $id): Match
    {
        $match = Match::with(['homeClub', 'awayClub'])->find($id);
        $diff = $match->homeClub->power + TournamentEnum::HOME_ADVANTAGE_POWER - $match->awayClub->power;
        $limits = [];
        if($diff > 0){
            $winFormula = (33 + $diff * 3.8);
            $drawFormula = (33 - $diff * 2.0);
            $limits[0] = $winFormula > 85 ? rand(79, 91) : rand($winFormula - 6, $winFormula + 6);
            $limits[1] = ($drawFormula + $limits[0]) > 95 ? rand(92,98) : rand($drawFormula + $limits[0] - 2, $drawFormula + $limits[0] + 2);

        }else{
            $winFormula = 100 - (33 + $diff * 4.1);
            $drawFormula = (33 - $diff * 1.8);
            $limits[1] = $winFormula < 15 ? rand(9, 21) : rand($winFormula - 6, $winFormula + 6);
            $limits[0] = ($limits[1] - $drawFormula) < 5 ? rand(2,8) : rand($limits[1] - $drawFormula - 2, $limits[1] - $drawFormula + 2);

        }

        $result = rand(1,100);

        if($result < $limits[0]){
            $match->home_club_goals = rand(1,5);
            $match->away_club_goals = rand(0, $match->home_club_goals - 1);
        }elseif($result >= $limits[0] && $result <= $limits[1]){
            $match->home_club_goals = rand(0,5);
            $match->away_club_goals = $match->home_club_goals;
        }else{
            $match->away_club_goals = rand(1,5);
            $match->home_club_goals = rand(0, $match->away_club_goals - 1);
        }

        $match->save();

        return $match;
    }
}
