<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match', function (Blueprint $table) {
            $table->id();
            $table->string('match_name')->nullable();
            $table->string('match_date')->nullable();
            $table->integer('match_id')->nullable();            
            $table->string('score-url')->nullable();
            $table->string('tv')->nullable();
            $table->string('bookmaker')->nullable();
            $table->string('fancy')->nullable();
            $table->string('inplay')->nullable();
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
        Schema::dropIfExists('match');
    }
}
