<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
		$destinationPath=public_path()."/match_json/";
       // $file = fopen("prod.json", "w");
		$file ='abcd_file.json';
		
		$url="http://3.7.102.54/listMarketBookBetfair/1.190824297,1.190815961,1.190660758,1.190660291,1.190660590,1.190818352,1.190816954,1.190622108,1.190622777,1.190623803,1.190670241,1.190618276,1.190621948,1.190621830";
		
		$headers = array('Content-Type: application/json');
		$process = curl_init();
		curl_setopt($process, CURLOPT_URL, $url);
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
		#curl_setopt($process, CURLOPT_HEADER, 1);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_HTTPGET, 1);
		#curl_setopt($process, CURLOPT_VERBOSE, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		$return = curl_exec($process);
		curl_close($process);
		//curl_setopt($process, CURLOPT_FILE, $fp);
		if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
      		File::put($destinationPath.$file,$return);
		\Log::info("Cron is working fine!");
		
    }
}
