<?php

namespace Serosensa\UserImage;

use Session;
use Storage;

/**
 * Class ImageService
 *
 * Handles all aspects of image upload processing.
 *
 * @see http://image.intervention.io/
 *
 * @package Serosensa\UserImage
 */
class MimeTypesService
{
    public function imageMimes() {
        return ['jpg', 'png', 'bmp', 'gif', 'svg'];
    }

    public function imageMimesForHumans(){
        $mimes = $this->imageMimes();
        return implode(', ', $mimes);
    }


    public function videoMimes(){
        return ['mp4','ogx','oga','ogv','ogg','webm','qt','m4v'];
    }

    public function videoMimesForHumans(){
        $mimes = $this->videoMimes();
        return implode(', ', $mimes);
    }
}