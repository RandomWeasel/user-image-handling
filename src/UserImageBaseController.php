<?php

namespace Serosensa\UserImage;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserImageBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var ImageService $imageService
     */
    public $imageService;

    /**
     * @var FileUploadService $fileUploadService
     */
    public $fileUploadService;

    public function __construct(ImageService $imageService, FileUploadService $fileUploadService){
        $this->imageService = $imageService;
        $this->fileUploadService = $fileUploadService;
    }
}
