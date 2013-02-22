<?php namespace Dwsla\ApiGateway;

use Illuminate\Support\ServiceProvider;

class ApiGatewayServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('dwsla/api-gateway');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['api-gateway'] = $this->app->share(function($app){
            
            $config = $app['config']['api-gateway::gateway'];
            $uri = ($config['site-root']) ?  $config['host'] ."/" . $config['site-root'] : $config['host'];
            $auth = $config['auth'];
            $adapter = $config['adapter'];
            
            $Gateway = new ApiGateway();
            $Gateway->setUri($uri);
            $Gateway->setAdapter($adapter);
            
            if ($auth) {
                $Gateway->setHeaders(array('Authentication: ' . $auth));
            }
            
            return $Gateway;
                
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}