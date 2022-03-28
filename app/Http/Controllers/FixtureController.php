<?php

namespace App\Http\Controllers;

use App\Http\Resources\FixtureResource;
use App\Models\Fixture;
use App\Services\FixtureService;
use App\Services\MatchService;
use App\Services\TournamentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class FixtureController extends Controller
{
    private $fixtureService;

    private $tournamentService;

    public function __construct(FixtureService $fixtureService, TournamentService $tournamentService)
    {
        $this->fixtureService = $fixtureService;
        $this->tournamentService = $tournamentService;
    }

    public function get(Request $request, string $fixtureId)
    {
        $fixture = $this->fixtureService->get((int) $fixtureId);
        if(!$fixture){
            return response('', 204);
        }

        return new FixtureResource($fixture);
    }

    public function getActive(Request $request, string $tournamentId)
    {
        $fixture = $this->fixtureService->getActive((int) $tournamentId);
        if(!$fixture){
            return response('', 204);
        }

        return new FixtureResource($fixture);
    }

    public function getAll(Request $request, string $tournamentId)
    {
        $fixtures = $this->fixtureService->getAll((int) $tournamentId, ['matches.homeClub', 'matches.awayClub']);

        return FixtureResource::collection($fixtures);
    }

    public function play(Request $request, string $fixtureId): FixtureResource
    {
        return new FixtureResource($this->fixtureService->getResults($fixtureId));

    }

    public function playAll(Request $request, string $tournamentId): AnonymousResourceCollection
    {
        return FixtureResource::collection($this->tournamentService->playAll($tournamentId));
    }
}
