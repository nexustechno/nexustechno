<?php

namespace App\Providers;

use App\Match;
use App\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {

        $main_url=explode(".",$request->getHost());
        if(count($main_url) == 3){
            unset($main_url[0]);
        }
        $this->website = Website::where('domain',implode(".",$main_url))->first();

        $this->app->singleton('website', function($app) {
            return $this->website;
        });

        $this->app->singleton('API_SERVER', function($app) {
            return 2;
        });

        $this->app->singleton('api_base_url', function ($app) {
            return "https://ballbets.xyz/api/v1";
        });
        $this->app->singleton('api_base_url2', function ($app) {
            return "https://ballbets.xyz/api/v1";
        });

        $matchesToBeDisplay = Match::where('status', 1)->where('winner', NULL)->pluck('event_id');

        // Sharing is caring
        View::share('website', $this->website);
        View::share('vue_app_version', 23);
        View::share('matchesToBeDisplay', $matchesToBeDisplay->toArray());

    }
}
