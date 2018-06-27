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

        <div class="panel-editImages">
            <div class="title-block">
                Edit Images
            </div>


            <div class="wrap-images">
                @foreach($images as $image)
                {{-- Use the components within a php/blade foreach loop, passing in data for one image --}}


                    <image-display :image="{{$image}}" :categories="{{$propertyImageTypes}}" image-path="/img/property-images/">


                        {{-- Creates an edit button and pop-over editor --}}
                        {{-- Use this WITHIN the image-display component to ensure the displayed data is updated when edits are saved --}}
                        <image-editor class="" :image="{{$image}}" post-url="{{route('propertyImageUpdate', $image->id)}}"  :categories="{{$propertyImageTypes}}">

                            {{-- All slots are optional - the categories slot is more complex --}}
                            {{-- Use these if you'd like to override default labels etc, or just leave them out --}}
                            <div slot="title" class="title">
                                Edit Image
                            </div>

                            <label slot="label_is_primary" class="label-long" for="is_primary" >
                                Make Main Image for Property:
                            </label>

                            <label slot="label_is_shown" class="label-long" for="is_primary">
                                Display this Image on the Property:
                            </label>

                            <div slot="subtext_caption" class="label-extraInfo">
                                Note: this field is ignored on RightMove for EPC images
                            </div>


                            {{-- CATEGORIES --}}
                            {{-- You must also pass in an array of categories in the opening tag of the image-editor --}}
                            <div slot="categories" slot-scope="categoriesScope" class="form-row">

                                {{-- This example creates radio buttons to select a category from the array passed in earlier --}}
                                <label for="">Image Type:</label>

                                <span v-for="category in categoriesScope.categories">
                                    <label for="category_id">@{{ option.name }}</label>
                                    <input type="radio" :value="category.id" v-model="categoriesScope.model.category_id">
                                </span>


                            </div>


                        </image-editor>

                    </image-display>

                @endforeach
            </div>




        </div>

        {{--You could also display existing images on this page--}}
    </div>


@stop

