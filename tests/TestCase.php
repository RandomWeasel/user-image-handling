<?php

namespace Serosensa\UserImage\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['Serosensa\UserImage\UserImageServiceProvider'];
    }
}
