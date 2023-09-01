<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoutBoxMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shout_box_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('nickname', 64)->nullable();
            $table->text('message');
            $table->string('ip', 64)->nullable();
            $table->string('fp', 64)->nullable();
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('shout_box_messages');
    }
}
