<?php

namespace Serosensa\UserImage\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedImage extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public $dates = ['created_at', 'updated_at'];
}
