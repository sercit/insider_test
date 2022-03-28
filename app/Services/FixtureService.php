<?php

namespace App\Services;

use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Models\Fixture;

class FixtureService
{
    public function create(array $data): Fixture
    {
        return Fixture::create($data);
    }

    public function createMany(array $fixtures): bool
    {
        return Fixture::insert($fixtures);
    }

    public function get(int $fixtureId, array $relations = []): ?Fixture
    {
        return Fixture::where('id', $fixtureId)->with($relations)->first();
    }

    public function getActive(int $tournamentId): ?Fixture
    {
        $fixture = Fixture::where('tournament_id', $tournamentId)
            ->whereDoesntHave('matches', function(Builder $query){
                $query->whereNotNull('home_club_goals');
            })->orderBy('week_id', 'ASC')->first();
        \Log::debug($fixture);
        return $fixture;
    }


    public function getAll(int $tournamentId, array $relations = []): Collection
    {
        $fixtures = Fixture::whereTournamentId($tournamentId)->exists();
        if(!$fixtures){
            $this->generate($tournamentId);
        }

        return Fixture::whereTournamentId($tournamentId)->with($relations)->get();
    }


    public function generate(int $tournamentId): bool
    {
        $tournamentService = app(TournamentService::class);
        $clubsCount = $tournamentService->getCountOfClubs($tournamentId);

        $fixtures = [];

        for($i = 1; $i < $clubsCount; $i++){
            $fixtures[] = [
                'tournament_id' => $tournamentId,
                'week_id' => $i,
            ];
        }
        for($i = 1; $i < $clubsCount; $i++){
            $fixtures[] = [
                'tournament_id' => $tournamentId,
                'week_id' => $clubsCount + $i - 1,
            ];
        }

        $fixtureService = app(FixtureService::class);

        return $fixtureService->createMany($fixtures);
    }

    private function getMatches(int $fixtureId): Collection
    {
        return Match::where('fixture_id', $fixtureId)->get();
    }

    public function getResults(int $fixtureId): Fixture
    {
        $matches = $this->getMatches($fixtureId);
        $matchService = app(MatchService::class);
        foreach ($matches as $match){
            $matchService->generateResult($match->id);
        }

        return $this->get($fixtureId, ['matches']);
    }

    public function getAllResults(Tournament $tournament): Collection
    {
        $results = collect();
        foreach ($tournament->fixtures as $fixture){
            $results->push($this->getResults($fixture->id));
        }

        return $results->sortBy('week_id');
    }
}
