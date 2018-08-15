<?php

namespace Serosensa\UserImage;

use Illuminate\Support\ServiceProvider;

class UserImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';

        // Publish assets
        $this->publishes([
            __DIR__ . '/js' => resource_path('assets/js/user-image')
        ], 'userimage-js-assets');

        $this->publishes([
            __DIR__ . '/scss' => resource_path('assets/sass/user-image')
        ], 'userimage-sass-assets');

        $this->publishes([
            __DIR__ . '/views' => resource_path('resources/views/user-image')
        ], 'userimage-views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the controllers

        // @TODO add to docs - how to access imageService in __construct
        // @TODO or another method to access ImageService?
        // @TODO facade to access ImageService?

        $this->app->singleton(ImageService::class);
        $this->app->singleton(FileUploadService::class);

        //Load packaged views
        $this->loadViewsFrom(__DIR__ . '/views', 'UserImage');
    }
}