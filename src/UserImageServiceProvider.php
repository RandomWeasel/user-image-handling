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
        include __DIR__.'/models/UploadedImage.php';

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

        
        //TODO publish base controller? or another method to access ImageService
        //TODO facade to access ImageService?

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
        $this->app->make('Serosensa\UserImage\FileUploadService');

        //Load packaged views
        $this->loadViewsFrom(__DIR__.'/views', 'UserImage');

        $this->loadMigrationsFrom(__DIR__.'/migrations'); //docs say to put this in boot

//        $this->app->register('\Intervention\Image');
//        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
//        $loader->alias('Image', 'Intervention\Image\Facades\Image');


    }
}
