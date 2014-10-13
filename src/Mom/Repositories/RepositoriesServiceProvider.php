<?php namespace Mom\Repositories;

use Illuminate\Support\ServiceProvider;
use Mom\Repositories\Helpers\AppNamespaceDetector;

class RepositoriesServiceProvider extends ServiceProvider {

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
        $this->registerArtisanCommand();
        $this->registerRepositories();
	}

    protected function registerRepositories()
    {
        $namespace = AppNamespaceDetector::getAppNamespace();
        foreach(glob(app_path() .'/Repositories/*.php') as $repositoryInterface) {
            $expl = explode("/", $repositoryInterface);
            $repositoryInterface = array_pop($expl);
            $expl = explode(".", $repositoryInterface);
            $repositoryInterface = $expl[0];

            if(file_exists(app_path() .'/Repositories/Eloquent/Eloquent'.$repositoryInterface.'.php')) {
                $this->app->bind($namespace.'Repositories\\'.$repositoryInterface, $namespace.'Repositories\\Eloquent\\Eloquent'.$repositoryInterface);
            }
        }
    }

    protected function registerArtisanCommand()
    {
        $this->app->bindShared('repositories.command.make', function($app)
        {
           return $app->make('Mom\Repositories\Console\RepositoryGenerateCommand');
        });

        $this->commands('repositories.command.make');
    }

    public function boot()
    {
        $this->register('mom/repositories');
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
