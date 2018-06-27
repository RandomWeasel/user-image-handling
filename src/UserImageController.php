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

    public function fetchFileUpload(Request $request){
        //TODO  - update to use a custom request rather than validating here

        $rules = [
            'file' => 'file' //appears to work, even if fails (eg file too large)
        ];

        $messages = [
//            'file' => 'The file was not uploaded successfully'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            //explicitly return json
            return response()->json(['errors' => $validator->errors()]);
        }


        //add the file destination to the form data
        $fileDest = $request->file_dest;
        $request->request->add(['file_dest' => $fileDest]);

        //save and rename the file
        $fileData = $this->fileUploadService->fileUpload($request);


        return response()->json([
            'success' => 'true',
            'message' => "File Uploaded",
            'fileData' => [
                'filename' => $fileData['filename'],
                'filetype' => $fileData['filetype'],
                'filesize' => $fileData['filesize'],
                'path' => $fileData['path']
            ]
        ]);


    }

    public function fetchImageUpload(Request $request){
//        dd($request->request,  $request->file_dest);
//        dd($request->file());


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



        //add the image destination to the form data
        $fileDest = $request->file_dest;
        $request->request->add(['file_dest' => $fileDest]);

        //save, rename and resize the image using the imageService
        $imagesData = $this->imageService->imageUpload($request, 'file', 1000);

//        dd($imagesData);

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





    public function imageUpload($parentId, IsValidImageRequest $request){

        //#1 - isValidImageRequest checks the filetypes are valid
        //on fail, it returns an error bag named $isValidImage

        //#2 - imageService renames, resizes, etc the images
        //See readme for details
        $imagesData = $this->imageService->imageUpload($request, 'images', 1000);

        //#3 - imageService returns an array of images (regardless of whether one or many images uploaded)
        //dd($imagesData);

        //#4 - save the image data to a database table as required
        //use ImageService to save to the default UploadedImages table
        $parent = YourModel::find($parentId)->get();
        $savedImages = $this->imageService->saveImageRecords($imagesData, $parent);


        #5 - return a response to the page - you may want to display the success message on the page
        //you could alternativel return json here
        return redirect()->back()->with('redirectMessage', 'Images Uploaded' );

    }

}
