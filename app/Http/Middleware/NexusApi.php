<?php
namespace App\Http\Middleware;
use App\Match;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class NexusApi
{
    /**

     * Handle an incoming request.

     *

     * @param  IlluminateHttpRequest  $request

     * @param  Closure  $next

     * @return mixed

     */

    public function handle($request, Closure $next)
    {

        try {
            $website = app('website');
            if($website->status == 0 && app('router')->current()->getName() != 'front'){
                return redirect()->to('/');
            }
        }catch (\Exception $e){

        }

        $prevRoue = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();

        if(app('router')->current()->getName() == $prevRoue || $request->ajax()){
            return $next($request);
        }

        if($prevRoue == 'matchDetail' || app('router')->current()->getName() == 'matchDetail'){

            if($prevRoue == 'matchDetail'){
                $broadcast = 0;
                $eventId = str_replace("/matchDetail/","",app('request')->create(url()->previous())->getPathInfo());
            }else{
                $broadcast = 1;
                $eventId = $request->route('id');
            }

            if(!empty($eventId)){

                $match = Match::where("event_id",$eventId)->first();

                if(!empty($match)) {
//                    if($eventId > 0 && $match->match_id)
                    {
                        $api_base_url = app('api_base_url');
                        $api_base_url2 = app('api_base_url2');
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

                        if($request->has('debug')) {
                            $ip = getenv('HTTP_CLIENT_IP') ?:
                                getenv('HTTP_X_FORWARDED_FOR') ?:
                                    getenv('HTTP_X_FORWARDED') ?:
                                        getenv('HTTP_FORWARDED_FOR') ?:
                                            getenv('HTTP_FORWARDED') ?:
                                                getenv('REMOTE_ADDR');

                            dd($ip);
                        }

                        $url = $baseUrl.'/websites/match/' . $eventId . "/" . $type . "/" . $broadcast . "/" . $match->match_id;

                        $res = $client->request('GET', $url);
                        $response = $res->getBody()->getContents();
//                        dd($response);
                    }
                }
            }
        }

        if($prevRoue == 'casinoDetail' || app('router')->current()->getName() == 'casinoDetail'){
            if($prevRoue == 'casinoDetail'){
                $explode = explode("/",trim(app('request')->create(url()->previous())->getPathInfo()));
                $broadcast = 0;
                $eventId = $explode[3];
            }else{
                $broadcast = 1;
                $eventId = $request->route('name');
            }

            if(!empty($eventId)){
                $client = new Client();
                $baseUrl = app('api_base_url2');
                $url = $baseUrl.'/websites/match/' . $eventId . "/casino/" . $broadcast . "/" . $eventId;
                $res = $client->request('GET', $url);
                $response = $res->getBody()->getContents();
            }
        }

        return $next($request);
    }

}
