{{--

    Displays the errors for the specified field and errorbag

    @include('forms._fieldErrors', ['field' => '', 'bag' => '']);

    errorbag can be null -
    @include('forms._fieldErrors', ['field' => '']);

--}}



@if( isset($bag) || $bag == '' )
    {{--Using an error bag--}}
    @if ($errors->$bag->messages()  && array_key_exists($field, $errors->$bag->messages()))

        @foreach($errors->$bag->messages()[$field] as $error)
            <div class="message-formError">
                {{ $error }}
            </div>
        @endforeach
    @endif

@else
    {{--No error bag--}}
    @if ($errors->first($field))

        @foreach($errors->get($field) as $error)
            <div class="message-formError">
                {{ $error }}
            </div>
        @endforeach
    @endif

@endif