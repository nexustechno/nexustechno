<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('match', function (Blueprint $table) {
        	$table->integer('status')->nullable()->default(1); //1 for active
        	$table->integer('action')->nullable()->default(1); //1 for unsuspend
			$table->integer('odds_limit')->nullable()->default(null); //1 for unsuspend
			$table->integer('min_bet_odds_limit')->nullable()->default(null); //1 for unsuspend
			$table->integer('max_bet_odds_limit')->nullable()->default(null); //1 for unsuspend
			$table->integer('min_bookmaker_limit')->nullable()->default(null); //1 for unsuspend
			$table->integer('max_bookmaker_limit')->nullable()->default(null); //1 for unsuspend
			$table->integer('min_fancy_limit')->nullable()->default(null); //1 for unsuspend
			$table->integer('max_fancy_limit')->nullable()->default(null); //1 for unsuspend
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
		Schema::table('match', function (Blueprint $table) {
        $table->dropColumn('status');
        $table->dropColumn('action');
		$table->dropColumn('odds_limit');
		$table->dropColumn('min_bet_odds_limit');
		$table->dropColumn('max_bet_odds_limit');
		$table->dropColumn('min_bookmaker_limit');
		$table->dropColumn('max_bookmaker_limit');
		$table->dropColumn('min_fancy_limit');
		$table->dropColumn('max_fancy_limit');
    });
    }
}
