<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Serosensa\UserImage;
use Serosensa\UserImage\Requests\IsValidImageRequest;
use Serosensa\UserImage\ImageService;

/* IMAGE HANDLING EXAMPLES
 * This controller contains examples of using the various functions of this package
 * Feel free to copy these examples into your own controller and modify as required
 * Remember you'll need to change Model and function names to match those of your own application
 * Please also see the README for full details on available options
 */

class ExampleController extends Controller
{

    public function __construct(ImageService $imageService)
    {
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


    //display existing images on a page - edit if required
    public function existingImages($parentId){

        //parent refers to the 'owner' of the image(s)
        //for example, the article to which the images belong

        //#1 - get all the images belonging to the same parent
        //this code relies on the parent model having an 'images' method
        $parent = Parent::findOrFail($parentId);
        $images = $parent->images;


        //#1a - get all the image categories (if required)
        //this functionality assumes a category_id field on each image
        $imageCategories = ImageCategories::all();


        //#3 - return the view with data
        return view('imageEditPage', compact('parent', 'images', 'imageCategories'));

    }


    //post the image editing forms to a route like this
    public function imageUpdate($imageId, Request $request){

        //parent refers to the 'owner' of the image(s)
        //for example, the article to which the images belong

        #1 - get the image from the database
        $image = Image::findOrFail($imageId);


        //#2 - validate here, or in a custom request, if required


        //#3a - get the rotation value for later before we clean the request
        $rotateAmount = $request->rotation;

        //#3b - clean the request as we're using 'update' (ensure fields are fillable)
        $request = $request->except('_method', '_token', 'rotation');


        //#4 - set checkbox defaults (overridden if checked)
        //as un-checked checkboxes are missing from the request
        if (!isset($request['is_primary'])) {
            $request['is_primary'] = 0;
        }

        if (!isset($request['is_shown'])) {
            $request['is_shown'] = 0;

            //if not shown, cannot be primary
            $request['is_primary'] = 0;
        }


        #5 - update the image in the database
        $image->update($request);


        #6 - update other related images, if required

        //if this image is_primary, remove this from all other images belonging to the parent
        //the below requires a 'parent' or equivalent method on the image model, and an images method on the parent model
        if ($request['is_primary'] == 1) {
            $otherImages = $image->parent->images->except($imageId);

            foreach ($otherImages as $anImage) {
                $anImage->is_primary = 0;
                $anImage->save();
            }
        }

        //#7 - call the imageService to rotate the image file (if required)
        //the imageService returns the actual created image file - can be used later if required
        $imageFile = $this->imageService->imageRotate($image, 'img/property-images/', $rotateAmount);



        //#8 - return response to the page - the message is displayed by the image-editor component
        return response()->json([
            'success' => 'true',
            'message' => 'Image Updated',
        ]);
    }
}
