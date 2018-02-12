@extends('layouts.app')

@section('header-title')
@stop

@section('content')

    <div class="content-main">

        {{-- Display messages --}}

        @if(session('redirectMessage'))

            <div class="message-redirect">
                {{session('redirectMessage')}}
            </div>

        @endif

        @if( count( $errors->getBags() ) > 0  )
            <div class="message-formError">
                There was a problem with the action you just attempted - please see specific errors in the relevant section of the page
            </div>
        @endif

        <div class="panel-addImages">
            <div class="title-block">
                Add Images
            </div>



            <p>You may upload multiple images at once by holding the CTRL key as you select each image file.  You can return to this screen and upload more images at a later date if required.
            </p>

            {{--Include a ready-made upload form & error block from Serosensa/userImages --}}
            @include('UserImage::_imageUploadForm', ['name' => 'images', 'label' => 'Choose Images:', 'route' => route('imageUpload', $parentId), 'errorBag' => 'isValidImage', 'uploadDir' => 'testuploads' , 'multiple' => 'false', 'class' => 'form-commonstyle' ])

        </div>

        {{--You could also display existing images on this page--}}
    </div>


@stop

