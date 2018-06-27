<?php

namespace Serosensa\UserImage\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IsValidImageRequest extends FormRequest
{

    protected $errorBag = 'isValidImage';


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        //TODO this fieldname is passed as a hidden input from the form - can this be accomplished another way and still allow different field names to be validated?
        $fieldName = $this->input('image_fieldname');

//        dd($this->input);

        return [
            $fieldName => 'required | image',
        ];
    }

    public function messages(){
        return [
            '*.image' => 'Please ensure all selected images are a valid image file type'
        ];
    }
}
