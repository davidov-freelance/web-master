<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapWebRoutes($router);

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace,
            // 'middleware' => 'web',
        ], function ($router) {
            require app_path('Http/routes.php');

            /*if ( $this->app->request->server('SERVER_NAME') == 'localhost' ) {
                if (
                    !$this->app->runningInConsole() &&
                    !array_key_exists(substr($this->app->request->getPathInfo(),1), \Route::getRoutes()->get( $this->app->request->getMethod() ) ) &&
                    is_file(storage_path('framework/routes.php'))
                ) {
                    require storage_path('framework/routes.php');

                    $uri = substr($this->app->request->getPathInfo(),1);

                    if (
                        array_key_exists($uri, \Route::getRoutes()->get( $this->app->request->getMethod() ))
                    ) {
                        $dbRoute = \DB::table('url_alias')->where('slug', '=', $uri)->first();
                        if ($dbRoute) {
                            $router->get($uri, function() use ($dbRoute) {
                                $segments   = explode('@', $dbRoute->map_to);
                                $controller = $segments[0];
                                $method     = $segments[1];
                                $obj        = $this->app->make($this->namespace . '\\' . $controller);
                                $params     = (!empty($dbRoute->params)) ? unserialize($dbRoute->params) : [];

                                return call_user_func_array([$obj, $method], $params);
                            });
                        }
                    }
                    unset($uri, $dbRoute);
                }
            }*/

        });
    }
}
