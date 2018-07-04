<?php
namespace Serosensa\UserImage;

use Serosensa\UserImage\UploadedImage;

use Carbon\Carbon; //dates and times

use Illuminate\Http\Request;
//use Intervention\Image\Image;
use \Storage;
use \Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;


class ImageService
{

    public function test(){
        return 'IMAGE SERVICE';
    }

    //upload, save, rename and resize images
    //pass the $request through to this function
    //optionally, pass a field name to fetch images from - if none, images is used
    //optionally, pass a max width (px) for the uploaded files to be resized to
    public function imageUpload(Request $request, $fileDest, $fieldName = null, $maxWidth = null )
    {

//        dd($fieldName, $request->file()); //works

        if( $request->file() == null ){
            //no files in this request - don't process, return null
            return null;

        } else {

            $destinationFolder = $fileDest;

            //remove any leading / character
            //as this prevents the resized image from being saved
            $destinationFolder = ltrim($destinationFolder, "/");


            //handle a custom field name for the images
            //if not set, then use default
            if( $fieldName != null){
                $uploadedFiles = $request->file($fieldName);



            } else {
                $uploadedFiles = $request->file('images');

            }

//            dd($uploadedFiles);

            $destinationPath = "$destinationFolder/"; // upload path

            //dd($uploadedFiles);

            //create a variable to hold the returned image data
            $imagesData = [];


            //set a default maxWidth if one was not passed
            if($maxWidth == null ){
                $maxWidth = 2000;
            }


            //create as seperate function to allow uploads of single or multiple files to be handled in the same function
            //then push the result into the $imagesData array
            function processFile($uploadedFile, $destinationPath, $maxWidth){

                //$uploadedFile = $request->file('image');

                $uploadedFileName = $uploadedFile->getClientOriginalName();


                // ensure a safe filename
                $fileName = preg_replace("/[^A-Z0-9._-]/i", "_", $uploadedFileName);

                //check if a file with that name already exists
                //if so, modify the name before uploading
                $i = 0;
                $parts = pathinfo($fileName); //break filename into array of consitutent parts


                while (Storage::disk('public')->exists($destinationPath . $fileName)) {
                    $i++; //increment value of $i
                    $fileName = $parts["filename"] . "-" . $i . "." . $parts["extension"]; //add the new value of $i to the filename, before the . extension
                }


                //save the file to the correct location
                Storage::disk('public')->put(
                    $destinationPath . $fileName, //set the path and desired filename
                    file_get_contents($uploadedFile->getRealPath())
                );



                $storedFile = Storage::disk('public')->get($destinationPath . $fileName);


                //image handling uses http://image.intervention.io/

                //resize and re-save the file
                //don't resize if is an svg as will error / not necessary
                if( $parts['extension'] != 'svg' ) {



//                    $intervention = new \Intervention\Image\Image;
//                    dd($intervention);
                    Image::make($storedFile)
//                ->orientate() //not sure if working - orient according to camera data
                        ->resize($maxWidth, null, function ($constraint){ //resize width
                            $constraint->aspectRatio(); //maintain aspect ratio
                            $constraint->upsize(); //prevent upsizing
                        })->save($destinationPath . $fileName);
                }  else {

//                //cannot save using Image if is an svg - so use Storage method
//                $uploadedFile->move($destinationPath, $fileName);
//                dd($fileName);
//                Storage::putFileAs($destinationPath, $uploadedFile, $fileName);

                }


                $imageData = [];
                $imageData['filename'] = $fileName;
                $imageData['filetype'] = $parts['extension'];
                $imageData['filesize'] = Storage::disk('public')->size($destinationPath . $fileName);
                $imageData['path'] = $destinationPath;

//            $imageData['caption'] = '';

//            dd(Storage::files($destinationPath));


//            dd($imageData);
                return $imageData;

            }


//            dd($uploadedFiles);

            //process each uploaded file - either single or in a foreach depending on whether a single file or array of files were uploaded
            //either way, the file data ends up in the $imagesData array
            if( getType($uploadedFiles) == 'array'){
                foreach( $uploadedFiles as $uploadedFile){
                    $imageData = processFile($uploadedFile, $destinationPath, $maxWidth);

                    $imagesData[] = $imageData;
                }
            } else {
                $imageData = processFile($uploadedFiles, $destinationPath, $maxWidth);

                $imagesData[] = $imageData;
            }



            //return the $imageData to be used as required by the calling function
            return($imagesData);
        }

    }


    /**
     * upload images with ajax / fetch
     * calls the standard imageUpload function and returns json
     *
     * @param Request $request
     * @param $fileDest
     * @param null $fieldName
     * @param null $maxWidth
     * @return mixed
     */
    public function fetchImageUpload(Request $request, $fileDest, $fieldName = null, $maxWidth = null){

        //TODO use proper Request for validation
        //validate
        $messages = [
            'file.image' => 'The logo file must be a valid image type (jpg, png, bmp, gif or svg)',
        ];

        $rules = [
            'file' => 'image'
        ];
//
        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            //explicitly return json
            return response()->json(['errors' => $validator->errors()]);
        }

        //save, rename and resize the image using the imageService
        $imagesData = $this->imageUpload($request, $fileDest, 'file', 1000);

        //TODO handle multiple files per field

        return response()->json([
            'success' => 'true',
            'message' => "Image Uploaded",
            'fileData' => [
                'filename' => $imagesData[0]['filename'],
                'filetype' => $imagesData[0]['filetype'],
                'filesize' => $imagesData[0]['filesize'],
                'path' => $imagesData[0]['path'],
            ]
        ]);



    }

    /**
     * accepts data for a single or multiple images (as array)
     * returns the saved database record(s) for the image(s)
     * returns either a single result or an array of results depending on input
     *
     * @param $imagesData
     * @param $parent
     * @return array
     */
    public function saveImageRecords($imagesData, $parent = null){
        //TODO - accept a $model var for own model, and save to a different table based on this

        $parentId = $parent->id;
        $parentModel = get_class($parent);//->getShortName();

        $saveRecord = function($imageData) use ($parentId, $parentModel){

            //only add the parent data if was set - otherwise, leave as-is (or null)
            if($parentModel != null &&  $parentId != null){
                $imageData->parent_model = $parentModel;
                $imageData->parent_id = $parentId;

            }

            $image = UploadedImage::updateOrCreate($imageData);

//            $image->filename = $imageData['filename'];
//            $image->filetype = $imageData['filetype'];
//            $image->filesize = $imageData['filesize'];
//            $image->path = $imageData['path'];
//            $image->save();

            return $image;
        };


        $savedImages = [];

        if(getType($imagesData) == 'array'){

            foreach($imagesData as $imageData){
                $image = $saveRecord($imageData);

                $savedImages[] = $image;
            }

            return $savedImages;

        } else {
            $image = $saveRecord($imagesData);

            $savedImage = $image;

            return $savedImage;
        }






    }


    public function imageRotate($image, $imagePath, $rotateAmount){

        if($rotateAmount != 0){

            $imageFilename = $image->filename;

            $imageFile = Image::make($imagePath.$imageFilename);
            $imageFile->rotate( - $rotateAmount);  //default rotation is opposite direction - so extra '-' to go other way

            $imageFile->save(); //overwrite original

            //returns the actual image object - can be manipulated further if required
            return $imageFile;

        }

    }

    /**
     * Handle the submission from the image-editor component
     *
     * @param $imageId
     * @param Request $request
     * @return mixed
     */
    public function imageEditorSave($image, Request $request){
        //parent refers to the 'owner' of the image(s)
        //for example, the article to which the images belong

//        #1 - get the image from the database
//        $image = Image::findOrFail($imageId);


        //#2 - get the rotation value for later before we clean the request
        $rotateAmount = $request->rotation;

        //#3 - clean the request as we're using 'update' (ensure fields are fillable)
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

            //TODO if no other images?

            foreach ($otherImages as $anImage) {
                $anImage->is_primary = 0;
                $anImage->save();
            }
        }

        //#7 - call the imageService to rotate the image file (if required)
        //the imageService returns the actual created image file - can be used later if required
        $imageFile = $this->imageRotate($image, $image->path, $rotateAmount);



        //#8 - return response to the page - the message is displayed by the image-editor component
        return response()->json([
            'success' => 'true',
            'message' => 'Image Updated',
        ]);
    }

}