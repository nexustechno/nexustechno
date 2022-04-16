<?php

namespace App\Http\Controllers\API;
use App\CreditReference;
use App\FancyResult;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SettingController;
use App\Match;
use App\MyBets;
use App\setting;
use App\Website;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{

    public function removeBroadCastEvent($eventId,$matchId,$status){

        if($status == 'hidden'){
            $broadcast = 0;
        }else{
            $broadcast = 1;
        }

        if(!empty($eventId)){

            $match = Match::where("event_id",$eventId)->first();

            $api_base_url = app('api_base_url');
            $api_base_url2 = app('api_base_url2');

            if(!empty($match)) {
                if($eventId > 0 && $match->match_id) {
                    $client = new Client();
                    $type = '';
                    if ($match->sports_id == 4) {
                        $type = 'cricket';
                        $baseUrl = $api_base_url;
                    } else if ($match->sports_id == 2) {
                        $type = 'tennis';
                        $baseUrl = $api_base_url2;
                    } else if ($match->sports_id == 1) {
                        $type = 'soccer';
                        $baseUrl = $api_base_url2;
                    }

                    if(isset($baseUrl)) {
                        $url = $baseUrl . '/websites/match/' . $eventId . "/" . $type . "/" . $broadcast . "/" . $match->match_id;

                        $res = $client->request('GET', $url);
                        $response = $res->getBody()->getContents();
                    }
                }
            }
        }
    }
}
