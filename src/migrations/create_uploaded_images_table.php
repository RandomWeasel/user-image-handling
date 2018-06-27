<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parent_model')->nullable();
            $table->integer('parent_id');
            $table->string('filename');
            $table->string('filetype')->nullable();
            $table->string('filesize')->nullable();
            $table->string('filesize')->nullable();
            $table->string('path');
            $table->integer('category_id')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(0);
            $table->boolean('is_shown')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_images');
    }
}
