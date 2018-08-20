<?php

namespace Serosensa\UserImage\Test;

use Serosensa\UserImage\ImageService;

class ImageServiceTest extends TestCase
{
    public function testItCanBeInstantiatedViaContainer()
    {
        $service1 = app(ImageService::class);
        $service2 = $this->app->make(ImageService::class);

        $this->assertInstanceOf(ImageService::class, $service1);
        $this->assertInstanceOf(ImageService::class, $service2);
    }

    public function testThatTestMethodReturnsString()
    {
        $service = $this->createService();
        $result = $service->test();

        $this->assertEquals($result, 'IMAGE SERVICE');
    }

    private function createService()
    {
        return app(ImageService::class);
    }
}
