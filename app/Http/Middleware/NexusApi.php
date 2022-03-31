<?php
namespace App\Http\Middleware;
use App\Match;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
                    if($eventId > 0 && $match->match_id) {
                        $client = new Client();
                        $type = '';
                        if ($match->sports_id == 4) {
                            $type = 'cricket';
                        } else if ($match->sports_id == 2) {
                            $type = 'tennis';
                        } else if ($match->sports_id == 1) {
                            $type = 'soccer';
                        }

                        $url = 'https://chatnexus.xyz/api/v1/websites/match/' . $eventId . "/" . $type . "/" . $broadcast . "/" . $match->match_id;

//                        dd($url);
                        $res = $client->request('GET', $url);
                        $response = $res->getBody()->getContents();
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
                $url = 'https://chatnexus.xyz/api/v1/websites/match/' . $eventId . "/casino/" . $broadcast . "/" . $eventId;
                $res = $client->request('GET', $url);
                $response = $res->getBody()->getContents();
            }
        }

        return $next($request);
    }

}
