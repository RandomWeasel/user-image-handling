<?php

namespace Serosensa\UserImage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserImageController extends UserImageBaseController
{
    public function test(){

//        $testData = 'Some Data from Controller';

        $testData = $this->imageService->test();

        return view('UserImage::someTestView', compact('testData'));
    }

}
