This package is incomplete

##Installation
- run `composer require serosensa/user-image-handling @dev`
- run `php artisan vendor:publish --tag=userimage-js-assets`
- add `require('./user-image/userImageApp.js')` to the main app.js file before the main vue instance is created but after vue is required
- run `php artisan vendor:publish --tag=userimage-sass-assets` 
- add `@import "_3vendor/serosensa/user-image"` in the vendor section of your main scss sheet (after your settings but before any custom styling)


##Usage & Functionality
Example controller Methods can be found in the ExampleController and copied from here if required
Uploading an image is considered as a separate step from creating a database record of the image.  Ensure both steps are completed so that images can be retrieved.
To access methods from eg the ImageService, extend the UserImageBaseController `class MyImageController extends UserImageBaseController`


###Upload an Image (Async)
Upload a file and receive json which can be used to request the image from the server
- Use the `file-upload-fetch` vue component
    **Props** 
    - postUrl (optional: default /fetch-file-upload)
    - fileDest - a writeable folder within the public disk 
    - parentIdentity (array: parent model, id) - used to ensure that the correct vue parent comp catches the event.  Not used in controller 
    - multiple (optional: default false)
- Displays a file-upload input 
- the controller reached by the postUrl should save the image file to storage.  The method `ImageService@fetchImageUpload` does this and returns json.  See _ImageService Methods_
- Expects to receive json with file data after upload.
- TODO display the uploaded files in this component, plus existing uploaded files (optional)
- Emits an event `file-upload` to event bus with parentIdentity and fileData on successful upload.  Parent can catch and use this data
//TODO Add this data to parent with v-model instead



###Upload an Image (Post)
- Use a regular file upload field posting to a controller.  
- The controller may use `ImageService@imageUpload` to rename and resize the file, and `ImageService@saveImageRecords` to create database records in the UploadedImages table  (see _ImageService Methods_)


###Save / Store an Image Record
- Once an image is uploaded, save a record of it to the relevant database table
- The data returned from `ImageService@imageUpload` may be used for this
- A default uploaded_images table is created by this package (see _Image Storage & Retieval_)
- The `ImageService@saveImageRecords` method can be used to save records to the default table (see _ImageService Methods_)
- There is also a default model for uploaded images (see _Default UploadedImages Model_)
- Alternatively, save a record to another table as required
- Images uploaded via the `file-upload-fetch` component will by default return the image data to the parent form.  Create database records in the controller saving the parent form.  Alternatively, set the file-upload-fetch postUrl to a controller which creates database records directly




###ImageService Methods

####ImageService@imageUpload 
**params** 
    - request object 
    - destination for files
    - name of field containing images (optional, default: images)
    - width to downscale image files to (optional, default: 2000px)
Must be run once per file input field in the form
Accepts either single or multiple images per file input.
Does not run validation - validate before calling
**Returns** array of file data, even if only 1 file in the field


####ImageService@fetchImageUpload
Uses `ImageService@imageUpload` - same params
Validates the image(s)
**Returns** json of file data (TODO handle multiple files per field)


####ImageService@saveImageRecords
//TODO not tested
Saves image records to the default `UploadedImages` table.  
**params** 
    - uploaded image data (single image)
    - parent model object  (optional, default:null)
Uploaded images data is expected to contain details such as filename and path.  The data returned from `ImageService@ImageUpload` is suitable
Accepts an array of images, or a single image 
TODO does this work?  is testing to see if image data is an array, but won't a single image still be an array?  alt text below
Accepts the data for a single image - should be run within a foreach loop for processing multiple images 
If a parent was sent, saves the name of the parent model and the parent Id to the image record.  Else, these are null. 
**Returns** array of records, or single record


####ImageService@imageEditorSave
Processes the data sent by the `image-editor` component
**params**
    - image instance (retreive the image in the calling controller and pass through)
    - request object
**Returns** json of image data to be used by the component



###Image Storage and Retreival 

####Default UploadedImages table
//TODO - have to manually copy the migration, not working in package
Stores a record of image data
Used by `ImageService@saveImageRecords` 
Optionally related to a parent model and id - this can be used to retrieve images from the table, eg by calling `UploadedImage::where(parent_model, 'article')` or `UploadedImage::where('parent_model', 'article')->where('parent_id', $parentId)`
Alternatively, store the `id` from this table in a pivot table to be accessed by any other model as required.
It is also 

**Required fields** 
    - filename
    - path
**Optional Fields**
    - filetype
    - filesize
    - category_id
    - caption
    - is_primary (default:0)
    - is_shown (default: 1)
    - parent_model - the name of the model which represents the parent
    - parent_id - the id of the parent


####Default UploadedImages model
Extend this model to add any additional functionality required - `class MyUploadedImageModel extends Serosensa\UserImage\UploadedImage`.
Sets dates and guarded on created_at and updated_at fields
 - methods?



###Display / Edit Existing Images

####Image-display Component
- Use the `image-display` vue component for each image (eg with v-for)
**Props** - image
- Displays the image and associated data
    - use slot `info-panel-default` to override default data
    - use slot `info-panel-extra` to add additional data
    - also has a default slot outside of the info panel
    
    
####Image-editor Component
- within the image-display component, add the `image-editor` component
**Props** 
    - image, 
    - postUrl (without image id or '/')
    - categories (optional)
- Allows for:
    - changing is_shown value
    - changing is_primary value
    - updating the caption
    - rotating the image
    - setting a category (optional via slot)
- Displays an edit button, with a modal to edit the image.  Has some default options and a number of slots
    - slot `title` 
    - slot `fields-default` displays the default edit fields.  Override this if required.
    - slot `categories` if a category selector is required.  If used, set `slot-scope="categoriesScope"`
    - slot `fields-extra` for any additional fields
    - slot `label_is_primary`
    - slot `label_is_shown`
    - slot `label_caption`
    - slot `subtext_caption` additional text below the caption
Validation should be carried out in the controller / custom request as normal.  On validation fail, laravel returns a json array of errors, which the editor window will display alongside the appropriate fields.

```
<image-display :image="{{$image}}" :categories="{{$propertyImageTypes}}" class="">

    <image-editor class="" :image="{{$image}}" post-url="{{route('propertyImageUpdate', $image->id)}}" :categories="{{$propertyImageTypes}}">
    
        <div slot="title" class="title">Edit Image</div>
    
    </image-editor>
</image-display>
```


#####Image Editor Categories
To display a category selector within the imageEditor:
- pass an array of categories the `categories` prop on image-editor
- each category must have at least an id and a name.  The component generates a categoryName using the 'name' field of each category
- ensure the image has a `category_id` field
- add `slot-scope="categoriesScope"` to the element utilising the slot, which allows sharing of data between the slot and the parent.
The example below creates radio buttons to select a category:
```
<div slot="categories" slot-scope="categoriesScope" class="form-row">
                                
    <label for="">Image Type:</label>

    <span v-for="category in categoriesScope.categories">
        <label for="category_id">{{ option.name }}</label>
        <input type="radio" :value="category.id" v-model="categoriesScope.model.category_id">
    </span>


</div>
```


####Field-errors Component
- use the `field-errors` component alongside any field that may return an error
- displays all errors for a given field 
- pass in the errors object and the name of the field to display errors for
```
<field-errors :errorObject="errors.is_primary"></field-errors>
```



-----------------------------------------------------
##Fuller details
//TODO - some of this is superceeded by the above / needs updating

###JS files (vue / general frontend)
- The file `userImageApp.js` registers all vue components provided by the package, 
- Export this file by running `php artisan vendor:publish --tag=userimage-js-assets` - this will export all js files to `resources/assets/js/user-image`.  
- WARNING if these files are directly modified as they may be over-written on package updates.  To force an update and overwrite files, add `--force` to the publish command
- Require this file in the applications' main app.js `require('./user-image/userImageApp.js');` before the main vue instance is created but after vue is required

###Styling
- include the _user-image.scss sheet in your main sass file.  Include this before your own style sheets so that you can easily override the styling 
- Publish this file by running `php artisan vendor:publish --tag=userimage-sass-assets` - this will export scss files to  `resources/assets/sass/user-image`

####Icons
- All icons in this package have the class `image-editor-icon`
- Icons can be re-coloured by setting values for `$user-image-icon-color-1` and `$user-image-icon-color-2` - set these values before pulling in the vendor style sheets (eg in settings) to override the defaults.  Some icons will only use color1.
- You may also override styles by class to style icons in different contexts
- Icons exist as js files in /vendor/serosensa/icons which are pulled in by each template as required



##Image Uploads - via ImageService
- the ImageService handles all aspects of image file uploads
- it must be passed the $request from the form in which image files were uploaded
- the service can handle multiple images being uploaded per field 
- the service can handle multiple file upload fields per form if unique field names are set
- each upload field can have its own destination folder
- destination folders should be .gitignored as they will change on the server independently of source code
- the imageService saves files to the 'public' disk defined in config/filesystems.  If this is the /app/public folder, files saved here are publicly visible via their url / filename


##File Destinations / Writeable folders
Folders in which uploaded files are to be saved must be writeable.  To do this, `chmhod -R 777 public/foldername`



###Image Upload forms
- Forms containing file uploads must have `enctype="multipart/form-data` or the parameter files => true (if using LaravelCollective forms)
- To upload multiple images per field, add `multiple` to the field and `[]` to the field name in the view file 



####Default Field names - one file upload field only
- by default, the `ImageService@imageUpload` function expects an upload field named `images`.  This default can be used if there is only one upload field in the request, or an alternative name can be used and passed to the service. 

####Custom Field names - one or more file upload fields
- the ImageService must be called once PER UPLOAD FIELD
- the file upload fields must have unique, custom names, eg `thumbnail`




###Image Upload in Controller
- See the ExampleController in the `/examples` directory for an example controller function
- The ImageService handles most of the image manipulation - the controller must simply call the ImageService, pass values, and save the resulting data.
- uploaded files should be validated (to ensure are correct image filetypes) before imageService@imageUpload is called.  This package includes an `IsValidImageRequest` which you may use.  This will return an errorbag `isValidImage` if validation fails.
- if imageService is mistakenly called with no files in the request, it will return null rather than generate an error
- the ImageService always returns a nested array of images data - regardless of number of files uploaded.  Loop this array to extract data for each image.

####ImageService@imageUpload Functionality
- Once Called, the imageservice:
 - loops each image
 - renames if a file by the same name already exists
 - saves the file to the specified folder (after stripping any leading /, as this causes issues with saving the resized file)
 - resizes the file to the specified max width (unless is an svg)
 - returns an array containing a data array for each image
 - this returned data should then be processed as required by the calling function - for instance, saving the filename to the database






##Image Rotation
- The `image-editor` component pulls in the `image-rotation` component, but this is also available separately.  
- The image-rotation component should be passed the database id of the image being editied, eg `<image-rotation :image-id="image.id"></image-rotation>`
- when a rotation button is clicked, the component emits an event which is collected by the editor and used to transform the image preview.
- a rotation value is added to the imageData and therefore passed to the controller where it can be used.  See `ExampleController` for a demo - the `ImageService` handles the actual rotation, so this functionality can be called anywhere.
- When edits are saved, the `image-display` component catches the event emitted from the image-editor.  This triggers a reload from disk of the updated image, so any changes (including rotation and any others which may be enabled in future) are reflected in the displayed image
- the `image-editor` component also reloads the image from disk and resets the rotation value, so it is 'fresh' when it is next loaded
- the reload from disk is accomplished by setting the src of the image to a `versionedImage` data object.  This is set to the orignal filename on load, but overwritten by calling the `makeVersionedImage()` function whenever the image should be updated (eg on save of edits).  This function appends a random version number to the end of the filename, causing a reload



-----------------------------------------------------
    
##TODO 
- Add form / styles package as a dependency (replace existing includes in this package?)
- IsValidImageRequest not properly tested - fetch file uploads validated in ImageService@fetchFileUpload



-----------------------------------------------------

##Useful References
- Creating a package - https://devdojo.com/blog/tutorials/how-to-create-a-laravel-package
- Laravel-specific package development, including info on publishing files - https://laravel.com/docs/5.6/packages
- Developing a package locally - https://johannespichler.com/developing-composer-packages-locally/