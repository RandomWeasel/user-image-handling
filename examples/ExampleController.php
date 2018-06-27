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




    /**
     * ajax image upload example
     * upload an image and return json of the image data
     *
     * @param Request $request
     * @return mixed
     */
    public function fetchImageUpload(Request $request){
//        dd($request->request,  $request->file_dest);
//        dd($request->file());

//        return response()->json([
//            'success' => 'true',
//            'message' => "Image Uploaded",
//            'fileData' => [
//                'filename' => $imagesData[0]['filename'],
//                'filetype' => $imagesData[0]['filetype'],
//                'filesize' => $imagesData[0]['filesize'],
//                'path' => $imagesData[0]['path'],
//            ]
//        ]);

        $fileDest = '/img/articles';

        $uploadedImageJson = $this->imageService->fetchImageUpload($request, $fileDest, 'file', 1000);

        return $uploadedImageJson;

    }


    /**
     * Ajax file upload example
     * based on the image upload
     * Use with caution - more work is needed (see fileUploadService)
     *
     * @param Request $request
     * @return mixed
     */
    public function fetchFileUpload(Request $request){

        $uploadedFileJson = $this->fileUploadService->fetchFileUpload();

        return $uploadedFileJson;

    }




    /**
     *
     * non-ajax image upload example
     * Upload an image / images and create a database record / records
     *
     * @param $parentId
     * @param IsValidImageRequest $request
     * @return mixed
     *
     */
    public function imageUpload($parentId, IsValidImageRequest $request){

        $fileDest = '/img/someModelImages'; //TODO set real file destination

        //#1 - isValidImageRequest checks the filetypes are valid
        //on fail, it returns an error bag named $isValidImage

        //#2 - imageService renames, resizes, etc the images
        //See readme for details
        $imagesData = $this->imageService->imageUpload($request, $fileDest, 'images', 1000);

        //#3 - imageService returns an array of images (regardless of whether one or many images uploaded)
        //dd($imagesData);

        //#4 - save the image data to a database table as required
        //use ImageService to save to the default UploadedImages table
        $parent = SomeModel::find($parentId)->get(); //TODO get real parent data
        $savedImages = $this->imageService->saveImageRecords($imagesData, $parent);


        #5 - return a response to the page - you may want to display the success message on the page
        return redirect()->back()->with('redirectMessage', 'Images Uploaded' );

    }

}
