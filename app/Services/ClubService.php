<?php

namespace App\Services;

use App\Enum\TournamentEnum;
use App\Models\Club;
use Illuminate\Support\Collection;

class ClubService
{
    public function getByTournament(int $tournamentId, array $relations = []): Collection
    {
        $clubs = Club::whereHas('tournaments', function($q) use($tournamentId) {
            $q->where('tournament_id', $tournamentId);
        })->with($relations)->get();

        $clubs->each(function ($club){
            $club->gd = $club->homeMatches->sum('home_club_goals') + $club->awayMatches->sum('away_club_goals') - $club->homeMatches->sum('away_club_goals') - $club->awayMatches->sum('home_club_goals');

            $club->homeMatches = $club->homeMatches->whereNotNull('home_club_goals')->mapToGroups(function($item){
                return [$item->home_points => $item];
            });

            $club->awayMatches = $club->awayMatches->whereNotNull('away_club_goals')->mapToGroups(function($item){
                return [$item->away_points => $item];
            });

            $club->wins = ($club->homeMatches->has(TournamentEnum::POINTS_WIN) ? $club->homeMatches[TournamentEnum::POINTS_WIN]->count() : 0)
                +
                ($club->awayMatches->has(TournamentEnum::POINTS_WIN) ? $club->awayMatches[TournamentEnum::POINTS_WIN]->count() : 0);


            $club->draws = ($club->homeMatches->has(TournamentEnum::POINTS_DRAW) ? $club->homeMatches[TournamentEnum::POINTS_DRAW]->count() : 0)
            +
                ($club->awayMatches->has(TournamentEnum::POINTS_DRAW) ? $club->awayMatches[TournamentEnum::POINTS_DRAW]->count() : 0);


            $club->loses = ($club->homeMatches->has(TournamentEnum::POINTS_LOSS) ? $club->homeMatches[TournamentEnum::POINTS_LOSS]->count() : 0)
            +
                ($club->awayMatches->has(TournamentEnum::POINTS_LOSS) ? $club->awayMatches[TournamentEnum::POINTS_LOSS]->count() : 0);

            $club->points = $club->wins * TournamentEnum::POINTS_WIN + $club->draws * TournamentEnum::POINTS_DRAW + $club->loses * TournamentEnum::POINTS_LOSS;
        });

        return $clubs->sortBy([
            function ($a, $b){
                return $b->points <=> $a->points;
            },function ($a, $b){
                return $b->gd <=> $a->gd;
            }
        ]);
    }

    public function getIdsByTournament(int $tournamentId): array
    {
        return $this->getByTournament($tournamentId)->pluck('id')->toArray();
    }
}
