<?php

namespace Serosensa\UserImage\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IsValidFileRequest extends FormRequest
{

    protected $errorBag = 'isValidFile';


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
