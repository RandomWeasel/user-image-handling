<?php

Route::get('user-image', function(){
    echo 'Hello from the User Image package!';
});

Route::get('user-image-test', 'Serosensa\UserImage\UserImageController@test');


//default upload routes
Route::post('fetch-file-upload', 'Serosensa\UserImage\UserImageController@fetchFileUpload'); //not fully tested / working


Route::post('fetch-image-upload', 'Serosensa\UserImage\UserImageController@fetchImageUpload');

Route::post('fetch-video-upload', 'Serosensa\UserImage\UserImageController@fetchVideoUpload');