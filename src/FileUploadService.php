<?php
namespace Serosensa\UserImage;

use Carbon\Carbon; //dates and times

use Illuminate\Http\Request;
//use Intervention\Image\Image;
use \Storage;
use \Session;
use Intervention\Image\Facades\Image;

class FileUploadService
{

    public function test(){
        return 'FILE UPLOAD SERVICE';
    }

    //TODO - file upload not fully tested, not necessarily robust, needs improvement and is not properly documented


    public function fileUpload($request, $fileDest){

        //process the file - save / rename
//        dd($request);
        $uploadedFile = $request->file('file');

//        dd($uploadedFile); //is a single file, not an array

        $destinationPath = "$fileDest/"; // upload path (within public)


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

        $fileData = [];
        $fileData['filename'] = $fileName;
        $fileData['filetype'] = $parts['extension'];
        $fileData['filesize'] = Storage::disk('public')->size($destinationPath . $fileName);

    }


    public function fetchFileUpload($request, $fileDest){

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


        //save and rename the file
        $fileData = $this->fileUploadService->fileUpload($request, $fileDest);

        //TODO - handle multiple files


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





}