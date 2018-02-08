
<?php

//default values if not set
if(! isset($submitButtonText) || $submitButtonText == ''){
    $submitButtonText = 'Upload';
}

if(! isset($submitButtonClass) || $submitButtonClass == ''){
    $submitButtonClass = 'button-save';
}

?>


<form action="{{$route}}" method="post" class="{{$class}}" enctype="multipart/form-data">


{{csrf_field()}}

<input type="hidden" name="{{$name}}_dest" value="{{$uploadDir}}">

    {{--Provides the name of the image field so that this can be validated--}}
    <input type="hidden" name="image_fieldname" value="{{$name}}">

<div class="form-row">
    <div class="form-col-half">

        @if($multiple == 'true')
            {{--Multiple file upload--}}
            <label for="{{$name}}[]">{{$label}}</label>
            <input type="file" name="{{$name}}[]" multiple="multiple">
        @else
            {{--Single file upload--}}
            <label for="{{$name}}">{{$label}}</label>
            <input type="file" name="{{$name}}">
        @endif

    </div>

    <div class="form-col-half">
        <input type="submit" value="{{$submitButtonText}}" class="{{$submitButtonClass}}">
    </div>
</div>


<div class="form-row">

    @include('UserImage::_formFieldErrors', ['field' => $name, 'bag' => $errorBag])

</div>


</form>
