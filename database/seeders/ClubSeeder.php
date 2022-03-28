<?php

namespace Database\Seeders;

use App\Models\Club;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        Club::insert([
            [
                'name' => 'Chelsea',
                'power' => 85,
                'created_at' => $now,
                'updated_at' => $now,
            ],[
                'name' => 'Liverpool',
                'power' => 89,
                'created_at' => $now,
                'updated_at' => $now,
            ],[
                'name' => 'Manchester City',
                'power' => 91,
                'created_at' => $now,
                'updated_at' => $now,
            ],[
                'name' => 'Arsenal',
                'power' => 79,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
