<?php

namespace App\Http\Controllers;

use App\Services\ClubService;
use App\Services\TournamentService;
use Illuminate\Http\Request;

class MainController extends Controller
{
    private $clubService;

    private $tournamentService;

    public function __construct(TournamentService $tournamentService, ClubService $clubService)
    {
        $this->tournamentService = $tournamentService;
        $this->clubService = $clubService;
    }

    public function index()
    {
        $tournament = $this->tournamentService->getActive();
        $clubs = $this->clubService->getByTournament($tournament->id);
        return view('main', compact('clubs'));
    }

    public function fixtures()
    {
        return view('fixtures');
    }

    public function table()
    {
        return view('table');
    }
}
