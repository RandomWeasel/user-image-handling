<?php

namespace Serosensa\UserImage;

use Illuminate\Support\ServiceProvider;

use Intervention\Image;

class UserImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //register the controllers
        $this->app->make('Serosensa\UserImage\UserImageBaseController');
        $this->app->make('Serosensa\UserImage\UserImageController');


        $this->app->make('Serosensa\UserImage\ImageService');

        //Load packaged views
        $this->loadViewsFrom(__DIR__.'/views', 'UserImage');

//        $this->app->register('\Intervention\Image');
//        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
//        $loader->alias('Image', 'Intervention\Image\Facades\Image');


    }
}
