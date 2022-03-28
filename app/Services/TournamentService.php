<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TournamentService
{
    public function getCountOfClubs(int $tournamentId): int
    {
        return Club::whereHas('tournaments',function($query) use ($tournamentId){
            $query->where('tournament_id', $tournamentId);
        })->count();
    }

    public function get(int $id, array $relations = []): ?Tournament
    {
        return Tournament::where('id',$id)->with($relations)->first();
    }

    public function getActive(array $relations = []): Tournament
    {
        return Tournament::orderByDesc('created_at')->with($relations)->first();
    }

    public function generateMatches(array $clubIds, ?Tournament $tournament ): bool
    {
        if($tournament->matches->count()){
            return false;
        }
        if($tournament === null){
            $tournament = $this->getActive();
        }

        $fixtures = $tournament->fixtures->pluck('id', 'week_id');
        $odd = false;
        if(count($clubIds) % 2 != 0){
            $odd = true;
            $clubIds[] = 0;
        }
        $count = count($clubIds);

        shuffle($clubIds);
        $prevRound = [];
        $matchService = app(MatchService::class);
        for($i = 1; $i <= $count - 1; $i++){
            for($j = 1; $j <= $count / 2; $j++){
                if($i == 1){
                    $club1 = $j;
                    $club2 = $count - $j + 1;
                }else{
                    $newClub1 = $prevRound[ 2 * $j - 2] + ($count / 2);
                    if($prevRound[2 * $j - 2] == $count){
                        $club1 = $count;
                    }elseif($newClub1 > $count - 1){
                        $club1 = $newClub1 - $count + 1;
                    }else{
                        $club1 = $newClub1;
                    }

                    $newClub2 = $prevRound[ 2 * $j - 1] + ($count / 2);
                    if($prevRound[2 * $j - 1] == $count){
                        $club2 = $count;
                    }elseif($newClub2 > $count - 1){
                        $club2 = $newClub2 - $count + 1;
                    }else{
                        $club2 = $newClub2;
                    }
                }
                if($i % 2 == 0){
                    $match = [$club2 - 1, $club1 - 1];
                    $prevRound[2 * $j - 1] = $club1;
                    $prevRound[2 * $j - 2] = $club2;
                }else{
                    $match = [$club1 - 1, $club2 - 1];
                    $prevRound[2 * $j - 1] = $club2;
                    $prevRound[2 * $j - 2] = $club1;
                }
                if(
                    (!$odd || $club1 != $clubIds[$count]) ||
                    (!$odd || $club2 != $clubIds[$count])
                ){
                    $matchService->create([
                        'home_club_id' => $clubIds[$match[0]],
                        'away_club_id' => $clubIds[$match[1]],
                        'fixture_id' => $fixtures[$i],
                    ]);

                    $matchService->create([
                        'home_club_id' => $clubIds[$match[1]],
                        'away_club_id' => $clubIds[$match[0]],
                        'fixture_id'  => $fixtures[($count - 1) * 2  - $i + 1],
                    ]);
                }
            }
        }
        return true;
    }

    public function getTable(Tournament $tournament): Collection {
        $clubService = app(ClubService::class);
        $fixtureIds = $tournament->fixtures->pluck('id')->toArray();
        $clubs = $clubService->getByTournament($tournament->id, ['homeMatches' => function ($query) use ($fixtureIds){
            $query->whereIn('fixture_id', $fixtureIds);
        },'awayMatches' => function ($query) use ($fixtureIds){
            $query->whereIn('fixture_id', $fixtureIds);
        }]);
        return $clubs;
    }

    public function playAll(int $tournamentId): Collection
    {
        $tournament = $this->get($tournamentId, ['fixtures' => function ($query){
            $query->whereHas('matches', function ($query){
                $query->whereNull('home_club_goals');
            });
        }]);
        $fixtureService = app(FixtureService::class);

        return $fixtureService->getAllResults($tournament);
    }

    public function reset(int $tournamentId): ?Tournament
    {
        Tournament::where('id', $tournamentId)->delete();
        $tournament = Tournament::create();
        $now = Carbon::now();
        $tournament->clubs()->syncWithPivotValues([1,2,3,4], [
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $tournament;
    }
}
