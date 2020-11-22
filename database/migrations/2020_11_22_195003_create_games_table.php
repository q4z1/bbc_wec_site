<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default('1'); // 1 = regular wec/s1 game , >5 for special games (finals, etc)
            $table->bigInteger('pos1');
            $table->bigInteger('pos2');
            $table->bigInteger('pos3');
            $table->bigInteger('pos4');
            $table->bigInteger('pos5');
            $table->bigInteger('pos6');
            $table->bigInteger('pos7');
            $table->bigInteger('pos8');
            $table->bigInteger('pos9');
            $table->bigInteger('pos10');
            $table->datetime('game_opened');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
