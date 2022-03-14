<?php

namespace App\Console\Commands;

use App\Casino;
use App\CasinoBet;
use App\Http\Controllers\CasinoCalculationController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateCasinoBetResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:update-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Declare casino pending bets winner';

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
        $bets = CasinoBet::whereNull("winner")->groupBy('casino_name')->orderBy('updated_at','ASC')->take(3)->get();

        $casinoCalculationController = new CasinoCalculationController();

        foreach ($bets as $bet){

            $apiURL = "http://3.6.94.71:3000/getresult/".$bet->casino_name;
            $response = Http::get($apiURL);
            $result = $response->json();

            $casino = Casino::where("casino_name",$bet->casino_name)->first();
            if(empty($casino)){
                return response()->json(['status'=>false,'message'=>'Casino not found']);
            }

            if(!isset($result['data'])){
                continue;
            }

            $resp = $casinoCalculationController->updateCasinoWinner($result['data'], $casino);

            $this->info(str_repeat("~=~",20));
            $this->info("casino: ".$bet->casino_name);
            $this->info($resp['message']);
            $this->info("\n");

        }
    }
}
