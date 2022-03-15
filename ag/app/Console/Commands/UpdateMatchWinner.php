<?php

namespace App\Console\Commands;

use App\Http\Controllers\SettingController;
use App\Match;
use Illuminate\Console\Command;

class UpdateMatchWinner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:update-winner-result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use to update completed match result';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $matches = Match::where('winner',null)->orderBy('updated_at','ASC')->where('id',2371487)->get();
        $matches = Match::where('winner',null)->orderBy('updated_at','ASC')->take(10)->get();
        if($matches->count() > 0){

            $settingController = new SettingController();

            foreach ($matches as $match){

                $this->info(str_repeat("~-~",20)."\n");
                $this->info("Match Id: ".$match->id);

                $apiResultData = app('App\Http\Controllers\RestApi')->getMatchResultData($match->match_id);

                if(isset($apiResultData[0]) && isset($apiResultData[0]['status']) && $apiResultData[0]['status'] != 'INACTIVE') {

                    if (!empty($apiResultData) && isset($apiResultData[0]) && isset($apiResultData[0]['runners']) && isset($apiResultData[0]['runners'][0]) && $apiResultData[0]['runners']!=null) {

                        $teams = explode(" v ", $match->match_name);
                        if (count($apiResultData[0]['runners']) >= 3) {
                            $teams[] = "The Draw";
                        }

                        foreach ($apiResultData[0]['runners'] as $key => $team) {
                            if ($team['status'] == 'WINNER') {
                                $settingController->updateMatchWinnerResult($match->id, $teams[$key]);
                                $match->updated_at = date('Y-m-d H:i:s');
                                $match->save();
                                $this->info($match->match_name . " => WINNER TEAM IS :::::  " . $teams[$key]);
                            }else{
                                $this->info($teams[$key] . " => ".$team['status']);
                            }
                        }
                    } else {
                        $match->updated_at = date('Y-m-d H:i:s');
                        $match->save();
                        $this->info($match->match_name . " => NULL");
                    }
                }else{
                    $match->updated_at = date('Y-m-d H:i:s');
                    $match->save();
                    $this->info($match->match_name . " => NULL");
                }
            }
        }
    }
}
