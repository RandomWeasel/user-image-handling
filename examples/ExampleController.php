<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Serosensa\UserImage;
use Serosensa\UserImage\Requests\IsValidImageRequest;
use Serosensa\UserImage\ImageService;

/* IMAGE HANDLING EXAMPLES
 * This controller contains examples of using the var functions of this package
 * Feel free to copy these examples into your own controller and modify as required
 * Please also see the README for full details on available options
 */

class ExampleController extends Controller
{

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }



    public function imageUploadForm($parentId){

        //$parentId would be, for example, the article the images belong to, if you're storing the images in a relational table with a foreign key.

        return view('imageUploadPage', compact('parentId')); //view in the main app
    }



    public function imageUpload($parentId, IsValidImageRequest $request){

        //#1 - isValidImageRequest checks the filetypes are valid
        //on fail, it returns an error bag named $isValidImage

        //#2 - imageService renames, resizes, etc the images
        //See readme for details
        $imagesData = $this->imageService->imageUpload($request, 'images', 1000);

        //#3 - imageService returns an array of images (regardless of whether one or many images uploaded)
        //dd($imagesData);

        //#4 - save the image data to a database table as required
        foreach($imagesData as $imageData){
            $newImage = new ImageModel(); //whatever the relevant model is for your image table

            $newImage->parent_id = $parentId;
            $newImage->filename = $imageData['filename'];
            $newImage->filetype = $imageData['filetype'];
            $newImage->filesize = $imageData['filesize'];
            $newImage->save();
        }

        #5 - return a response to the page - you may want to display the success message on the page
        return redirect()->back()->with('redirectMessage', 'Images Uploaded' );

    }
}
