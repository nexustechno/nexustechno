<?php
namespace App\Http\Middleware;
use App\Match;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class BackendNexusApi
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

        if($prevRoue == 'backpanel.risk-management-details' || app('router')->current()->getName() == 'backpanel.risk-management-details'){

            if($prevRoue == 'backpanel.risk-management-details'){
                $broadcast = 0;
                $id = str_replace("/risk-management-details/","",app('request')->create(url()->previous())->getPathInfo());
            }else{
                $broadcast = 1;
                $id = $request->route('id');
            }

            if(!empty($id)){

                $match = Match::where("id",$id)->first();

                if(!empty($match)) {
                    if($match->event_id > 0 && $match->match_id) {
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

                        $url = $baseUrl.'/websites/match/' . $match->event_id . "/" . $type . "/" . $broadcast . "/" . $match->match_id;

//                        dd($url);
                        $res = $client->request('GET', $url);
                        $response = $res->getBody()->getContents();
                    }
                }
            }
        }

//        dd(app('router')->current()->getName());

        if($prevRoue == 'casinoDetail' || app('router')->current()->getName() == 'casinoDetail'){
            if($prevRoue == 'casinoDetail'){

                $explode = explode("/",trim(app('request')->create(url()->previous())->getPathInfo()));
                $broadcast = 0;
                $eventId = $explode[3];
            }else{
                $broadcast = 1;
                $eventId = $request->route('name');
            }

//            dd($eventId);

            if(!empty($eventId)){
                $client = new Client();
                $baseUrl = app('api_base_url2');
                $url = $baseUrl.'/websites/match/' . $eventId . "/casino/" . $broadcast . "/" . $eventId;

//                dd($url);
                $res = $client->request('GET', $url);
                $response = $res->getBody()->getContents();

            }
        }

        return $next($request);
    }

}
