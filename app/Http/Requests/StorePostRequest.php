<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StorePostRequest extends FormRequest
{
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
        return [
            'body' => 'required',
            'file' => 'nullable'
        ];
    }
    public function failedValidation(Validator $v)
        {
            throw new HttpResponseException(response()->json([
                'status'=> false,
                'message'=> 'Validation error',
                'data'=> $v->errors()
            ]));
        }
}
