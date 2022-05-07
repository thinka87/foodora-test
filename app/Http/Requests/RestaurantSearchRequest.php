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
            'restaurant_name' => 'string|nullable|present',
            'cuisine'=> 'string|nullable|present',
            'city' => 'string|nullable|present',
            'distance'=> 'numeric|nullable|min:0|present',
            'longitude' => 'numeric|between:-180,180|nullable|present',
            'latitude'=> 'numeric|between:-90,90|nullable|present',
            'search_text'=> 'string|nullable|present',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ],400));
    }
}
