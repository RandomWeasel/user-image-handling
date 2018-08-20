<?php

namespace Serosensa\UserImage\Test;

use Serosensa\UserImage\ImageService;
use Serosensa\UserImage\FileUploadService;
use Serosensa\UserImage\UserImageBaseController;

class UserImageBaseControllerTest extends TestCase
{
    public function testItCanBeInstantiatedViaContainer()
    {
        $service1 = app(UserImageBaseController::class);
        $service2 = $this->app->make(UserImageBaseController::class);

        $this->assertInstanceOf(UserImageBaseController::class, $service1);
        $this->assertInstanceOf(UserImageBaseController::class, $service2);
    }

    public function testItHasServiceDependencyProperties()
    {
        $controller = $this->createController();

        $this->assertInstanceOf(ImageService::class, $controller->imageService);
        $this->assertInstanceOf(FileUploadService::class, $controller->fileUploadService);
    }

    /**
     * @return UserImageBaseController
     */
    private function createController()
    {
        return app(UserImageBaseController::class);
    }
}
