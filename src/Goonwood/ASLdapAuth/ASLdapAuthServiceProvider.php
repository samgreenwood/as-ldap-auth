<?php namespace Goonwood\ASLdapAuth;

use Illuminate\Support\ServiceProvider;

class ASLdapAuthServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['LdapAuth'] = $this->app->share(function($app)
        {
            return new ASLdapAuthServiceProvider;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth');
	}

}