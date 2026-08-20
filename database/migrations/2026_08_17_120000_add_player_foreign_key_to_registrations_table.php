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
        // match exactly. Since Laravel 11 ->change() no longer needs doctrine/dbal, so the
        // raw MySQL statement that used to stand here is gone - it could not run on SQLite
        // and broke the test database.
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('player_id')->nullable(false)->change();

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

        Schema::table('registrations', function (Blueprint $table) {
            $table->integer('player_id')->nullable(false)->change();
        });
    }
}
