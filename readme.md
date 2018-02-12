This package is incomplete


##Image Uploads - via ImageService
- the ImageService handles all aspects of image file uploads
- it must be passed the $request from the form in which image files were uploaded
- the service can handle multiple images being uploaded per field 
- the service can handle multiple file upload fields per form if unique field names are set
- each upload field can have its own destination folder
- destination folders should be .gitignored as they will change on the server independently of source code
- the imageService saves files to the 'public' disk defined in config/filesystems.  If this is the /app/public folder, files saved here are publicly visible via their url / filename

###Image Upload Pre-made Form
- This package supplies a form to upload images, use this as is with an include (below) or see the 'Custom Forms' section to create your own forms.
- `@include('UserImage::_imageUploadForm', ['name' => '', 'label' => '', 'route' => '', 'uploadDir' => '' , 'multiple' => '', 'class' => ''])` 

- The form takes a number of parameters:

    - `name` - the name value of the field (must be unique if there are multiple upload fields per page)
    - `label` - The label to display alongside the field
    - `route` - The route the form submits to.  Must be a POST route
    - `errorBag` - The name of the validation error bag within which the controller will return errors.  Leave empty to use the default bag
    - `uploadDir` - The directory within public/img which files should be uploaded to.  Must exist prior to file uploads.  To not include first /
    - `multiple` - true or false, whether multiple files can be uploaded at once
    - `class` - any class names to attach to the form
    - `submitButtonText` - Text to display on the submit button.  Leave blank for default
    - `submitButtonClass` - Class for the submit button.  Leave blank for default
    
    

###Image Upload Custom forms
- Forms containing file uploads must have `enctype="multipart/form-data` or the parameter files => true (if using LaravelCollective forms)
- To upload multiple images per field, add `multiple` to the field and `[]` to the field name in the view file


####Default Field names - one file upload field only
- the file upload field must be named `images`
- a hidden field to set the upload folder (within public/img) must be populated and named `images_dest`
- call the ImageService and pass the $request to it - eg
  `$this->imageService->imageUpload($request)`


####Custom Field names - one or more file upload fields
- the ImageService must be called once PER UPLOAD FIELD
- the file upload fields must have unique, custom names, eg `thumbnail`
- a hidden field to set the upload folder (within public/img) must be present for each upload field.  This must be named the same as the related filed upload field, followed by `_dest` - eg `thumbnail_dest`
- call the ImageService and pass the $request and the custom fieldname to it - eg `$this->imageService->imageUpload($request, 'thumbnail')`



###Image Upload in Controller
- See the ExampleController in the `/examples` directory for an example controller function
- The ImageService handles most of the image manipulation - the controller must simply call the ImageService, pass values, and save the resulting data.
- uploaded files should be validated (to ensure are correct image filetypes) before imageService is called.  This package includes an `IsValidImageRequest` which you may use.  This will return an errorbag `isValidImage` if validation fails.
- When ImageService is called, pass:
    - the $request object containing the the uploaded files
    - The name of the field containing the images.  This is optional and defaults to 'images'
    - a max width (px) (optional).  If this is not passed, the default is 2000px
- if imageService is mistakenly called with no files in the request, it will return null rather than generate an error
- the ImageService always returns a nested array of images data - regardless of number of files uploaded.  Loop this array to extract data for each image.

####ImageService Functionality
- Once Called, the imageservice:
 - loops each image
 - renames if a file by the same name already exists
 - saves the file to the specified folder
 - resizes the file to the specified max width (unless is an svg)
 - returns an array containing a data array for each image
 - this returned data should then be processed as required by the calling function - for instance, saving the filename to the database





    
##TODO 
- Add form / styles package as a dependency (replace existing includes in this package?)
- 