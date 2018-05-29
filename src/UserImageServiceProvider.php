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

        //publish assets
        $this->publishes([
        __DIR__.'/js' => resource_path('assets/js/user-image')
        ], 'userimage-js-assets');


        $this->publishes([
            __DIR__.'/scss' => resource_path('assets/sass/user-image')
        ], 'userimage-sass-assets');


        $this->publishes([
            __DIR__.'/views' => resource_path('resources/views/user-image')
        ], 'userimage-views');

        //publish the controller too?

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
