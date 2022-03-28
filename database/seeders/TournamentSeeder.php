<?php

namespace Database\Seeders;

use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tournament = Tournament::create();
        $now = Carbon::now();
        $tournament->clubs()->syncWithPivotValues([1,2,3,4], [
            'created_at' => $now,
            'updated_at' => $now,
        ]);

    }
}
