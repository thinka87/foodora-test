<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RestaurantSearchRequest extends FormRequest
{
    public function rules()
    {
        return [
            'restaurant_name' => 'string|nullable',
            'cuisine'=> 'string|nullable',
            'city' => 'string|nullable',
            'distance'=> 'integer|min:0',
            'longitude' => 'numeric|between:-180,180|nullable',
            'latitude'=> 'numeric|between:-90,90|nullable',
            'search_text'=> 'string|nullable',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
