<?php
namespace App\Modules\Comment\Providers;

use App;
use Config;
use Lang;
use View;
use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{
	/**
	 * Register the Comment module service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// This service provider is a convenient place to register your modules
		// services in the IoC container. If you wish, you may make additional
		// methods or service providers to keep the code more focused and granular.
		App::register('App\Modules\Comment\Providers\RouteServiceProvider');

		$this->registerNamespaces();
	}

	/**
	 * Register the Comment module resource namespaces.
	 *
	 * @return void
	 */
	protected function registerNamespaces()
	{
		Lang::addNamespace('comment', realpath(__DIR__.'/../Resources/Lang'));

		View::addNamespace('comment', realpath(__DIR__.'/../Resources/Views'));
	}
}
