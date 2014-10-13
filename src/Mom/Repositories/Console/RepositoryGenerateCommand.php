<?php namespace Mom\Repositories\Console;

use Illuminate\Console\Command;
use Mom\Repositories\Helpers\AppNamespaceDetector;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\File;

class RepositoryGenerateCommand extends Command {

    protected $name = 'repositories:generate';

    protected $description = 'Generate a new repository';

    protected $generator;

    protected $wantsEloquent = false;

    public function __construct(RepositoryGenerator $repositoryGenerator)
    {
        $this->generator = $repositoryGenerator;
        parent::__construct();
    }

    public function fire()
    {
        $repositories = $this->getRepositories();
        $data['namespace'] = $this->getNamespace();

        if($this->wantEloquentImplementation()) {
            $this->wantsEloquent = true;
        }

        foreach($repositories as $repository) {
            $data['repository'] = $repository;

            if(!File::exists(app_path().'/Repositories')) {
                File::makeDirectory(app_path().'/Repositories');
            }

            $this->generator->create($data, __DIR__.'/stubs/repositoryInterface.stub', app_path().'/Repositories/'.$repository.'Repository.php');

            if($this->wantsEloquent) {

                if(!File::exists(app_path().'/Repositories/Eloquent')) {
                    File::makeDirectory(app_path().'/Repositories/Eloquent');
                }

                $data['type'] = "Eloquent";
                $data['attributes'] = 'protected $modelName = \''.$repository.'\';';

                if(!file_exists(app_path().'/Repositories/Eloquent/AbstractEloquentRepository.php')) {
                    $this->generator->create($data, __DIR__.'/stubs/abstractRepository.stub', app_path().'/Repositories/Eloquent/AbstractEloquentRepository.php');
                }

                $this->generator->create($data, __DIR__.'/stubs/repositoryClass.stub', app_path().'/Repositories/Eloquent/Eloquent'.$repository.'Repository.php');
            }
        }

    }

    private function wantEloquentImplementation()
    {
        return $this->confirm('Do you want to create Eloquent repositories? [yes|no]');
    }

    private function getRepositories()
    {
        $repos = $this->argument('repositories');

        if(empty($repos)) {
            $repos = $this->addRepository();
            $this->line('');
        }

        return $repos;
    }

    private function getNamespace()
    {
        return AppNamespaceDetector::getAppNamespace();
    }

    private function addRepository()
    {
        $repos[] = $this->ask('Please enter a name for the repository you want to create: ');
        $this->line('');

        if ($this->confirm('Do you want to add another repository? [yes|no]'))
        {
            $this->line('');
            $repos = array_merge($repos, $this->addRepository());
        }

        return $repos;
    }

    protected function getArguments()
    {
        return [
            ['repositories', InputArgument::IS_ARRAY, 'The repositories you wish to generate, comma seperated.']
        ];
    }
} 