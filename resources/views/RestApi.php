<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use GuzzleHttp\Client;



class RestApi extends Controller

{

    //

	public function GetMatchOdds($marketid)

    {

		$client = new Client();

		$alldata=$client->request('GET','http://3.7.102.54/listMarketBookBetfair/'.$marketid,[

			'headers'=>[

			'Content-Type' => 'application/json',

			]

	   ]);

   		$data=json_decode($alldata->getBody(), true);

		return $data;

	}
	/*public function DetailCall($matchId)

    {

		$client = new Client();

		$alldata=$client->request('GET','http://139.162.20.164:3000/getDFancy?eventId=30555193',[

			'headers'=>[

			'Content-Type' => 'application/json',

			]

	   ]);

   		$data=json_decode($alldata->getBody(), true);

		return $data;

	}*/

	public function DetailCall($eventId,$matchId,$matchtype)
	{
		
		if($matchtype==1)
		{
			try {
				$client = new Client();
					$alldata=$client->request('GET','http://139.162.20.164:3000/getDFancy?eventId='.$eventId,[
						'headers'=>[
						'Content-Type' => 'application/json',
					]
			   ]);
				$data=json_decode($alldata->getBody(), true);
				//print_r($data);
				return $data;
			}
			catch (\Guzzle\Http\Exception\ConnectException $e) {
				//$response = json_encode((string)$e->getResponse()->getBody());
				echo 'cache1';
				return 0;
			}
			catch (Exception $e)
			{
				echo 'cache2';
				return 0;
			}
		}
		else
		{
			try {
				$client = new Client();
					$alldata=$client->request('GET','http://69.30.238.2:3644/odds/multiple?ids='.$matchId,[
						'headers'=>[
						'Content-Type' => 'application/json',
					]
			   ]);
				
				$data=json_decode($alldata->getBody(), true);
				//print_r($data);
				return $data;
			}
			catch (\Guzzle\Http\Exception\ConnectException $e) {
				//$response = json_encode((string)$e->getResponse()->getBody());
				return 0;
			}
			catch (Exception $e)
			{
				return 0;
			}
		}

	}

	public function GetAllMatch()

    {

		$client = new Client();

		$alldata=$client->request('GET','http://3.7.102.54/oddslist',[

			'headers'=>[

			'Content-Type' => 'application/json',

			]

	   ]);

   		$data=json_decode($alldata->getBody(), true);

		return $data;

	}

	

}

