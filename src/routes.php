<?php

Route::get('user-image', function(){
    echo 'Hello from the User Image package!';
});

Route::get('user-image-test', 'Serosensa\UserImage\UserImageController@test');