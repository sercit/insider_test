<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_club_id');
            $table->foreign('home_club_id')->references('id')->on('clubs');

            $table->unsignedBigInteger('away_club_id');
            $table->foreign('away_club_id')->references('id')->on('clubs');

            $table->unsignedBigInteger('fixture_id');
            $table->foreign('fixture_id')->references('id')->on('fixtures');


            $table->smallInteger('home_club_goals')->nullable();
            $table->smallInteger('away_club_goals')->nullable();
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
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['home_club_id', 'away_club_id', 'fixture_id']);
        });
        Schema::dropIfExists('matches');
    }
}
