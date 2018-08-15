<?php

namespace Serosensa\UserImage\Test;

use Serosensa\UserImage\FileUploadService;

class FileUploadServiceTest extends TestCase
{
    public function testItCanBeInstantiatedViaContainer()
    {
        $service1 = app(FileUploadService::class);
        $service2 = $this->app->make(FileUploadService::class);

        $this->assertInstanceOf(FileUploadService::class, $service1);
        $this->assertInstanceOf(FileUploadService::class, $service2);
    }

    public function testThatTestMethodReturnsString()
    {
        $service = $this->createService();
        $result = $service->test();

        $this->assertEquals($result, 'FILE UPLOAD SERVICE');
    }

    public function testFileUploadHandlesNonRequest()
    {
        $service = $this->createService();
//        $result = $service->fileUpload(null, "foo");

        // $this->assert.............
    }

    public function testFileUploadHandlesNoUpload()
    {
        $service = $this->createService();

        $request = new \Illuminate\Http\Request();

//        $result = $service->fileUpload($request, "foo");

        // $this->>assert(....................
    }

    /**
     * @return FileUploadService
     */
    private function createService()
    {
        return app(FileUploadService::class);
    }
}
