<?php namespace Mom\Repositories\Helpers;

class AppNamespaceDetector {

    public static function getAppNamespace()
    {
        $composer = (array) json_decode(file_get_contents(base_path().'/composer.json', true));

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path)
        {
            if (app_path() == realpath(base_path().'/'.$path)) return $namespace;
        }

        return 'App\\';
    }

} 