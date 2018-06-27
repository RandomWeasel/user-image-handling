This package is incomplete

##Installation
- run `composer require serosensa/user-image-handling @dev`
- run `php artisan vendor:publish --tag=userimage-js-assets`
- add `require('./user-image/userImageApp.js')` to the main app.js file before the main vue instance is created but after vue is required
- run `php artisan vendor:publish --tag=userimage-sass-assets` 
- add `@import "_3vendor/serosensa/user-image"` in the vendor section of your main scss sheet (after your settings but before any custom styling)


##Usage & Functionality

###Upload an Image (Async)
- Use the `file-upload-fetch` vue component
    **Props** 
    - postUrl (optional: default /fetch-file-upload),
    - fileDest
    - parentIdentity (array: parent model, id), 
    - multiple (optional: default false)
- Displays a file-upload input 
- Emits an event `file-upload` to event bus with parentIdentity and fileData on successful upload.  Parent can catch and use this data
- the controller reached by the postUrl 

- Saves the image to the location specified by fileDest prop if the default post controller is used

###Upload an Image (Post)
- Use a regular file upload field posting to a controller.  The controller 

####Save / Store an Image Record
- 
 
 `UserImageController@fetchFileUpload` to save the file and return json of file data.  Catch this data in the parent form and create any database records required in this forms controller.  Or, submit to a custom route and save data there
- If not using the file-upload-fetch component, you can utilise the `imageService->imageUpload($request)` method to rename and resize an uploaded image.  See ImageService section
- Optionally, use the `imageService->createImageRecord()` method to save a database record of the image


###Display / Edit Existing Images
- Use the `image-display` vue component for each image (eg with v-for)
    **Props** - image
- Displays the image and associated data
    - use slot `info-panel-default` to override default data
    - use slot `info-panel-extra` to add additional data
    - also has a default slot outside of the info panel
    
- within the image-display component, add the `image-editor` component
    **Props** - image, postUrl, categories (optional)
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
    - slot `subtext_caption`


###Routes & Controller Functions
- Send 

-----------------------------------------------------
##Fuller details

###JS files (vue / general frontend)
- The file `userImageApp.js` registers all vue components provided by the package, 
- Export this file by running `php artisan vendor:publish --tag=userimage-js-assets` - this will export all js files to `resources/assets/js/user-image`.  
- These files should not be directly modified as they will be over-written on package updates.  To force an update, add `--force` to the publish command
- Require this file in the applications' main app.js `require('./user-image/userImageApp.js');` before the main vue instance is created but after vue is required

###Styling
- include the _user-image.scss sheet in your main sass file.  Include this before your own style sheets so that you can easily override the styling 
- Publish this file by running `php artisan vendor:publish --tag=userimage-sass-assets` - this will export scss files to  `resources/assets/sass/user-image`

####Icons
- All icons in this package have the class `image-editor-icon`
- Icons can be re-coloured by setting values for `$user-image-icon-color-1` and `$user-image-icon-color-2` - set these values before pulling in the vendor style sheets (eg in settings) to override the defaults.  Some icons will only use color1.
- You may also override styles by class to style icons in different contexts
- Icons exist as js files in /vendor/serosensa/icons which are pulled in by each template as required

-----------------------------------------------------

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

-----------------------------------------------------

##Existing Images - Displaying / Editing
- see `examples/imageUploadPage.blade.php`` for a full usage example, including all options
- Display images using the image-display component - use this within a  php foreach loop or v-for of images, passing the data for a single image to the component
```
<image-display :image="{{$image}}" :categories="{{$propertyImageTypes}}" class="">
</image-display>
```

- This component displays the image, caption, and other details such as if is main image
- Optionally, pass an array of categories to the component.  A categoryName will be generated using the `name` field of each category and requires a `category_id` field on the image.
- To allow editing, include the image-editor component between the open/close tags of the image-display component.  This creates an edit button and pop-up editor window

```
<image-editor class="" :image="{{$image}}" post-url="{{route('propertyImageUpdate', $image->id)}}" :categories="{{$propertyImageTypes}}">

    <div slot="title" class="title">Edit Image</div>

</image-editor>
```
- Pass in the data for a single image, the url / route the edit form should post to (must be a POST route), and optionally an array of categories.
- There are a number of slots within this component, all of which are optional.  See the example file for usage examples.  These are:
    - title             -   title for popover
    - categories        -   scoped slot within which category selection inputs can be created (see example).  Requires a category_id field on the image
    - label_is_primary  -   custom label text
    - label_is_shown    -   custom label text
    - subtext_caption   -   additional text below the caption
- When used as a pair, these two components display a preview of the image with various extra details and an edit button.  When edits are saved, the preview data also updates.
- Additional classes can be passed to style either or both components if required.  Some basic styles are included.
- Validation should be carried out in the controller / custom request as normal.  On validation fail, laravel returns a json array of errors, which the editor window will display alongside the appropriate fields.


##Image Rotation
- The `image-editor` component pulls in the `image-rotation` component, but this is also available separately.  
- The image-rotation component should be passed the database id of the image being editied, eg `<image-rotation :image-id="image.id"></image-rotation>`
- when a rotation button is clicked, the component emits an event which is collected by the editor and used to transform the image preview.
- a rotation value is added to the imageData and therefore passed to the controller where it can be used.  See `ExampleController` for a demo - the `ImageService` handles the actual rotation, so this functionality can be called anywhere.
- When edits are saved, the `image-display` component catches the event emitted from the image-editor.  This triggers a reload from disk of the updated image, so any changes (including rotation and any others which may be enabled in future) are reflected in the displayed image
- the `image-editor` component also reloads the image from disk and resets the rotation value, so it is 'fresh' when it is next loaded
- the reload from disk is accomplished by setting the src of the image to a `versionedImage` data object.  This is set to the orignal filename on load, but overwritten by calling the `makeVersionedImage()` function whenever the image should be updated (eg on save of edits).  This function appends a random version number to the end of the filename, causing a reload

-----------------------------------------------------

##Misc
- **Field Errors** - the field-errors component displays all error messages for a given field.  Simply pass in the errors object and the name of the field to display errors for.
```
<field-errors :errorObject="errors.is_primary"></field-errors>
```

-----------------------------------------------------
    
##TODO 
- Add form / styles package as a dependency (replace existing includes in this package?)
- 



-----------------------------------------------------

##Useful References
- Creating a package - https://devdojo.com/blog/tutorials/how-to-create-a-laravel-package
- Laravel-specific package development, including info on publishing files - https://laravel.com/docs/5.6/packages
- Developing a package locally - https://johannespichler.com/developing-composer-packages-locally/