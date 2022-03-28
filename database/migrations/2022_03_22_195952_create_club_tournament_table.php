<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_tournament', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->foreign('club_id')->references('id')->on('clubs');

            $table->unsignedBigInteger('tournament_id');
            $table->foreign('tournament_id')->references('id')->on('tournaments');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('club_tournament', function (Blueprint $table) {
            $table->dropForeign(['club_id', 'tournament_id']);
        });

        Schema::dropIfExists('club_tournament');
    }
}
