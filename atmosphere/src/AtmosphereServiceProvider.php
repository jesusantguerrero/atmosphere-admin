<?php

namespace Wave;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;

class AtmosphereServiceProvider extends ServiceProvider
{

	public function register(){

	    // $loader = AliasLoader::getInstance();
	    // $loader->alias('Atmosphere', WaveFacade::class);

	    // $this->app->singleton('atmosphere', function () {
	    //     return new Atmosphere();
	    // });

	    // $this->loadHelpers();

        // $this->loadLivewireComponents();

	    // $atmosphereMiddleware = [
	    // 	\Illuminate\Auth\Middleware\Authenticate::class,
    	// 	\Wave\Http\Middleware\TrialEnded::class,
    	// 	\Wave\Http\Middleware\Cancelled::class,
    	// ];

    	// $this->app->router->aliasMiddleware('token_api', \Wave\Http\Middleware\TokenMiddleware::class);
	    // $this->app->router->pushMiddlewareToGroup('web', \Wave\Http\Middleware\WaveMiddleware::class);
        // $this->app->router->pushMiddlewareToGroup('web', \Wave\Http\Middleware\InstallMiddleware::class);

	    // $this->app->router->middlewareGroup('atmosphere', $atmosphereMiddleware);
	}

	public function boot(Router $router, Dispatcher $event){
		Relation::morphMap([
		    'users' => config('atmosphere.user_model')
		]);
        Relation::morphMap([
		    'teams' => config('atmosphere.team_model')
		]);

		if(!config('wave.show_docs')){
			Gate::define('viewLarecipe', function($user, $documentation) {
	            	return true;
	        });
	    }

        $this->mergeConfigFrom(__DIR__ . '/../config/atmosphere.php', 'journal');
        // $this->publishConfig();
        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__.'/../docs/', 'docs');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'atmosphere');
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
	}

     /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }

	protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    protected function loadMiddleware()
    {
        foreach (glob(__DIR__.'/Http/Middleware/*.php') as $filename) {
            require_once $filename;
        }
    }
}
