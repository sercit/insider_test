<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClubCollection;
use App\Http\Resources\ClubResource;
use App\Http\Resources\TournamentResource;
use App\Models\Fixture;
use App\Models\Match;
use App\Models\Tournament;
use App\Services\ClubService;
use App\Services\FixtureService;
use App\Services\TournamentService;
use Illuminate\Http\Request;

class TournamentController extends Controller
{

    private $tournamentService;

    private $tournament;

    private $clubService;

    private $fixtureService;

    public function __construct(TournamentService $tournamentService, ClubService $clubService, FixtureService $fixtureService){
        $this->tournamentService = $tournamentService;
        $this->fixtureService = $fixtureService;
        $this->clubService = $clubService;
        $this->tournament = $this->tournamentService->getActive();
    }

    public function getClubs(Request $request, int $tournamentId): ClubCollection{
        $tournament = $this->tournamentService->get($tournamentId, ['fixtures.matches']);
        return new ClubCollection(
            $this->clubService
                ->getByTournament(
                    $tournament->id)
        );
    }

    public function generate(Request $request, int $tournamentId){

        $tournament = $this->tournamentService->get($tournamentId);

        $fixtureService = app(FixtureService::class);
        $fixtureService->generate($tournament->id);

        $clubs = $this->clubService->getIdsByTournament($tournament->id);
        if($this->tournamentService->generateMatches($clubs, $tournament)){
            return response('', 200);
        }
        return response('', 400);
    }

    public function getTable(Request $request, int $tournamentId)
    {
        $tournament = $this->tournamentService->get($tournamentId, ['fixtures']);
        $table = $this->tournamentService->getTable($tournament);

        return ClubResource::collection($table);
    }

    public function getActive(Request $request)
    {
        $tournament = $this->tournamentService->getActive(['fixtures.matches']);
        return new TournamentResource($tournament);
    }

    public function reset(Request $request, string $tournamentId)
    {
        return new TournamentResource($this->tournamentService->reset((int)$tournamentId));
    }
}
