<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_commands', function (Blueprint $table) {
            $table->integer('sessionId')->unsigned();
            $table->integer('commandId')->unsigned();
            $table->primary(['sessionId', 'commandId']);
            $table->foreign('sessionId')->references('id')->on('sessions');
            $table->foreign('commandId')->references('id')->on('commands');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_commands');
    }
}
