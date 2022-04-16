<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
class RestApi extends Controller
{
	public function getteen20LastResult($casino)
    {
    	if($casino == 'ab20'){
    		$url='http://143.244.135.148/json/andar_bahar/last_result.json';
    	}elseif($casino == 'teen20'){
    		$url='http://143.244.135.148/json/20teenpati/last_result.json';
    	}elseif($casino == 'dt202'){
    		$url='http://143.244.135.148/json/dragontiger2/last_result.json';
    	}elseif($casino == 'baccarat'){
    		$url='http://143.244.135.148/json/baccarat/last_result.json';
    	}elseif($casino == '32card'){
    		$url='http://143.244.135.148/json/32cardsb/last_result.json';
    	}

		try
		{
			$client = new Client();
				$alldata=$client->request('GET',$url,[
					'headers'=>[
					'Content-Type' => 'application/json',
				]
		   ]);
			$data=json_decode($alldata->getBody(), true);
			return $data;
		}
		catch (\Guzzle\Http\Exception\ConnectException $e) {
			return 0;
		}
		catch (Exception $e)
		{

			return 0;
		}
    }
    public function Get32cardvideo()
    {
    	$client = new Client();
		$alldata=$client->request('GET','http://143.244.135.148/json/32cardsb/32_cards_b.json',[
			'headers'=>[
			'Content-Type' => 'application/json',
			]
	   ]);
   	   $data=json_decode($alldata->getBody(), true);
	   return $data;
    }
	public function GetTeen20Data($casino)
    {
    	if($casino == 'ab20'){
    		$url='http://143.244.135.148/json/andar_bahar/andar_bahar.json';
    	}elseif($casino == 'teen20'){
    		$url='http://143.244.135.148/json/20teenpati/20teenpati.json';
    	}elseif($casino == 'baccarat'){
    		$url='http://143.244.135.148/json/baccarat/baccarat.json';
    	}elseif($casino == '32card'){
    		$url='http://172.105.253.130/json/32cardsb/odd.json';
    	}elseif($casino == 'dt202'){
    		$url='http://143.244.135.148/json/dragontiger2/dragon_tiger_2.json';
    	}

		try
		{
			$client = new Client();
				$alldata=$client->request('GET',$url,[
					'headers'=>[
					'Content-Type' => 'application/json',
				]
		   ]);
			$data=json_decode($alldata->getBody(), true);
			return $data;
		}
		catch (\Guzzle\Http\Exception\ConnectException $e) {
			return 0;
		}
		catch (Exception $e)
		{

			return 0;
		}
    }
	/*public function GetMatchOdds($marketid)
    {
		$url='http://3.7.102.54/listMarketBookBetfair/'.$marketid;
		try {
			$client = new Client();
			$alldata=$client->request('GET',$url,[
				'headers'=>[
				'Content-Type' => 'application/json',
				]
			]);
			$data=json_decode($alldata->getBody(), true);
			return $data;
		}
		catch (\Guzzle\Http\Exception\ConnectException $e) {
			return 0;
		}
		catch (Exception $e)
		{
			return 0;
		}
	}*/
	public function DetailCall($eventId,$matchId,$matchtype)
	{
		/*echo "aab";
		echo $eventId;
		exit;*/

		if($matchtype==1)
		{
			$url='http://69.30.238.2:3644/odds/multiple?ids='.$eventId;
			try {
				$client = new Client();
					$alldata=$client->request('GET',$url,[
						'headers'=>[
						'Content-Type' => 'application/json',
					]
			   ]);
				$data=json_decode($alldata->getBody(), true);
				return $data;
			}
			catch (\Guzzle\Http\Exception\ConnectException $e) {
				return 0;
			}
			catch (Exception $e)
			{
				return 0;
			}
		}
		else
		{
			$url='http://69.30.238.2:3644/odds/multiple?ids='.$eventId;
			try {
				$client = new Client();
					$alldata=$client->request('GET',$url,[
						'headers'=>[
						'Content-Type' => 'application/json',
					]
			   ]);

				$data=json_decode($alldata->getBody(), true);
				return $data;
			}
			catch (\Guzzle\Http\Exception\ConnectException $e) {
				return 0;
			}
			catch (Exception $e)
			{
				return 0;
			}
		}
	}
	public function GetAllMatch($match_type = null)
	{
		if($match_type && $match_type == 4){
			return $this->getCricketData();
		} else if($match_type && $match_type == 2){
			return $this->getTennisData();
		} else {
			return $this->getSoccerData();
		}

		$client = new Client();
		$alldata = $client->request('GET', 'http://3.7.102.54/oddslist', [
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
		$data = json_decode($alldata->getBody(), true);
		return $data;
	}
	public function Fancy_and_Bookmaker_DetailCall($eventId,$matchId,$matchtype)
	{
		$url='http://3.7.102.54/DaimondApi/'.$eventId;
		try
		{
			$client = new Client();
				$alldata=$client->request('GET',$url,[
					'headers'=>[
						'Content-Type' => 'application/json',
						]
					]);
					$data=json_decode($alldata->getBody(), true);
					return $data;
				}
		catch (\Guzzle\Http\Exception\ConnectException $e) {
			return 0;
		}
		catch (Exception $e)
		{

			return 0;
		}
	}
	public function getCricketData()
	{
		$client = new Client();
		$alldata = $client->request('GET', 'http://139.177.188.73:3000/getcricketmatches', [
			'headers' => [
				'Content-Type' => 'application/json',
			]

		]);
		$data = json_decode($alldata->getBody(), true);
		return $data;
	}
	public function getTennisData()
	{
		$client = new Client();
		$alldata = $client->request('GET', 'http://139.177.188.73:3000/gettennismatches', [
			'headers' => [
				'Content-Type' => 'application/json',
			]

		]);
		$data = json_decode($alldata->getBody(), true);
		return $data;
	}
	public function getSoccerData()
	{
		$client = new Client();
		$alldata = $client->request('GET', 'http://139.177.188.73:3000/getsoccermatches', [
			'headers' => [
				'Content-Type' => 'application/json',
			]

		]);
		$data = json_decode($alldata->getBody(), true);
		return $data;
	}
	public function getSingleCricketMatchData($eventId,$matchId,$matchtype){

        if($matchtype == 4)
        { //Cricket
            $url='http://139.177.188.73:3000/getBM?eventId='.$eventId;
        } else if($matchtype== 2)
        { //tennis
            $url='http://172.104.179.219:3000/getdata/'.$matchId;
        } else if($matchtype== 1)
        { //soccer
            $url='http://172.104.179.219:3000/getdata/'.$matchId;
        }

        try {
            $client = new Client();
            $alldata=$client->request('GET',$url,[
                'headers'=>[
                    'Content-Type' => 'application/json',
                ]
            ]);
            $data = json_decode($alldata->getBody(), true);
            return $data;
        }
        catch (\Guzzle\Http\Exception\ConnectException $e) {
            return 0;
        }
        catch (Exception $e)
        {
            return 0;
        }
	}

	public function getMatchResultData($matchId,$matchtype=4){

        $url='http://104.199.199.1/pre_api/results.php?betfair_id='.$matchId;

        try {
            $client = new Client();
            $alldata=$client->request('GET',$url,[
                'headers'=>[
                    'Content-Type' => 'application/json',
                ]
            ]);
            $data = json_decode($alldata->getBody(), true);

            return $data;
        }
        catch (\Guzzle\Http\Exception\ConnectException $e) {
            return 0;
        }
        catch (Exception $e)
        {
            return 0;
        }
	}

    public function getSingleMatchData($eventId,$matchId,$matchtype){
        $api_base_url = app('api_base_url');
        $api_base_url2 = app('api_base_url2');
        if($matchtype == 4)
        { //cricket
            $url=$api_base_url.'/match/cricket/detail/'.$eventId."/".$matchId;
        } else if($matchtype== 2)
        { //tennis
            $url=$api_base_url2.'/match/tennis/detail/'.$eventId."/".$matchId;
        } else if($matchtype== 1)
        { //soccer
            $url=$api_base_url2.'/match/soccer/detail/'.$eventId."/".$matchId;
        }

        try {
            $client = new Client();
            $alldata=$client->request('GET',$url,[
                'headers'=>[
                    'Content-Type' => 'application/json',
                ]
            ]);
            $data = json_decode($alldata->getBody(), true);
            return $data['data'];
        }
        catch (\Guzzle\Http\Exception\ConnectException $e) {
            return 0;
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    public function getSingleMatchOddsData($eventId,$matchId,$matchtype){
        $api_base_url = app('api_base_url');
        $api_base_url2 = app('api_base_url2');
        if($matchtype == 4)
        { //cricket
            $url=$api_base_url.'/match/cricket/detail/'.$eventId."/odds";
        } else if($matchtype== 2)
        { //tennis
            $url=$api_base_url2.'/v1/match/tennis/detail/'.$matchId."/odds";
        } else if($matchtype== 1)
        { //soccer
            $url=$api_base_url2.'/match/soccer/detail/'.$matchId."/odds";
        }

        try {
            $client = new Client();
            $alldata=$client->request('GET',$url,[
                'headers'=>[
                    'Content-Type' => 'application/json',
                ]
            ]);
            $data = json_decode($alldata->getBody(), true);

            if(isset($data['data']))
                return $data['data'];
            else
                return [];
        }
        catch (\Guzzle\Http\Exception\ConnectException $e) {
            return 0;
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    public function getSingleMatchFancyData($eventId,$matchId,$matchtype){
        $api_base_url = app('api_base_url');
        $api_base_url2 = app('api_base_url2');
        if($matchtype == 4)
        { //cricket
            $url=$api_base_url.'/match/cricket/detail/'.$eventId."/fancy";
        } else if($matchtype== 2)
        { //tennis
            $url=$api_base_url2.'/match/tennis/detail/'.$matchId."/fancy";
        } else if($matchtype== 1)
        { //soccer
            $url='https://nexusapi.xyz/api/v1/match/soccer/detail/'.$matchId."/fancy";
        }

        try {
            $client = new Client();
            $alldata=$client->request('GET',$url,[
                'headers'=>[
                    'Content-Type' => 'application/json',
                ]
            ]);
            $data = json_decode($alldata->getBody(), true);
            return $data['data'];
        }
        catch (\Guzzle\Http\Exception\ConnectException $e) {
            return 0;
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    public function getSingleMatchBookmakerData($eventId,$matchId,$matchtype){
        $api_base_url = app('api_base_url');
        $api_base_url2 = app('api_base_url2');
        if($matchtype == 4)
        { //cricket
            $url=$api_base_url.'/v1/match/cricket/detail/'.$eventId."/bookmaker";
        } else if($matchtype== 2)
        { //tennis
            $url=$api_base_url2.'/v1/match/tennis/detail/'.$matchId."/bookmaker";
        } else if($matchtype== 1)
        { //soccer
            $url=$api_base_url2.'/match/soccer/detail/'.$matchId."/bookmaker";
        }

        try {
            $client = new Client();
            $alldata=$client->request('GET',$url,[
                'headers'=>[
                    'Content-Type' => 'application/json',
                ]
            ]);
            $data = json_decode($alldata->getBody(), true);
            return $data['data'];
        }
        catch (\Guzzle\Http\Exception\ConnectException $e) {
            return 0;
        }
        catch (Exception $e)
        {
            return 0;
        }
    }
}
