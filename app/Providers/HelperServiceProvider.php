<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->helpers(app_path('Helpers'.DIRECTORY_SEPARATOR));
    }

    public function helpers($folder)
    {
        $recursive_directory_iterator = new RecursiveDirectoryIterator($folder);
        $recursive_iterator  = new RecursiveIteratorIterator($recursive_directory_iterator);

        while ($recursive_iterator->valid()) {
            if (
                ! $recursive_iterator->isDot() &&
                $recursive_iterator->isFile() &&
                $recursive_iterator->isReadable() &&
                $recursive_iterator->current()->getExtension() === 'php' &&
                strpos($recursive_iterator->current()->getFilename(), 'Helper')
            ) {
                require $recursive_iterator->key();
            }

            $recursive_iterator->next();
        }
    }
}
