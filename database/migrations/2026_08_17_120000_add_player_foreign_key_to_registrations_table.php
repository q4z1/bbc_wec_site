<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPlayerForeignKeyToRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Deleted players used to leave their registrations behind, because player_id
        // was a plain integer without a foreign key. Clear any leftovers first, otherwise
        // the constraint below cannot be created.
        DB::table('registrations')
            ->whereNotIn('player_id', DB::table('players')->select('id'))
            ->delete();

        // players.id is BIGINT UNSIGNED ($table->id()), so the referencing column has to
        // match exactly. doctrine/dbal is not installed, hence the raw statement instead
        // of ->change().
        DB::statement('ALTER TABLE registrations MODIFY player_id BIGINT UNSIGNED NOT NULL');

        Schema::table('registrations', function (Blueprint $table) {
            $table->foreign('player_id')
                ->references('id')
                ->on('players')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
        });

        DB::statement('ALTER TABLE registrations MODIFY player_id INT NOT NULL');
    }
}
