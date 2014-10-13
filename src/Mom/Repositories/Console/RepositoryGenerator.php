<?php namespace Mom\Repositories\Console;


use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;

class RepositoryGenerator {

    protected $file;

    protected $mustache;

    public function __construct(Filesystem $file, Mustache_Engine $mustache)
    {
        $this->file = $file;
        $this->mustache = $mustache;
    }

    public function create(array $data, $template, $destination)
    {
        $template = $this->file->get($template);

        $stub = $this->mustache->render($template, $data);

        $this->file->put($destination, $stub);
    }

} 