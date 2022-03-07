<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivilegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privilege', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('list_client')->nullable();
            $table->integer('main_market')->nullable();
            $table->integer('manage_fancy')->nullable();
            $table->integer('fancy_history')->nullable();
            $table->integer('match_history')->nullable();
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
        Schema::dropIfExists('privilege');
    }
}
