<?php
namespace Serosensa\UserImage;

use Carbon\Carbon; //dates and times

use Illuminate\Http\Request;
use \Storage;
use \Session;
use Intervention\Image;

class ImageService
{

    public function test(){
        return 'IMAGE SERVICE';
    }

    //upload, save, rename and resize images
    //pass the $request through to this function
    //optionally, pass a field name to fetch images from - if none, images is used
    //optionally, pass a max width (px) for the uploaded files to be resized to
    public function imageUpload(Request $request, $fieldName = null, $maxWidth = null )
    {

        if( $request->file() == null ){
            //no files in this request - don't process, return null
            return null;

        } else {
            //handle a custom field name for the images
            //if not set, then use default
            //also
            if( $fieldName != null){
                $uploadedFiles = $request->file($fieldName);
                $destinationFolder = $request->input($fieldName . "_dest"); //set in the form
            } else {
                $uploadedFiles = $request->file('images');
                $destinationFolder = $request->input('images_dest'); //set in the form
            }


            $destinationPath = "img/$destinationFolder/"; // upload path

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

//            $imageData['caption'] = '';

//            dd(Storage::files($destinationPath));


//            dd($imageData);
                return $imageData;

            }


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


}