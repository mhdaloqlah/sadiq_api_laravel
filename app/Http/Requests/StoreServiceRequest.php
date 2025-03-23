<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'required|max:255|string',
            'brief' => 'required|max:255|string',
            'category_id' => 'required',
            'user_id' => 'required',
            'description' => 'sometimes|string',
            'status' => 'sometimes',
            'location' => 'sometimes|string',
            'views_number' => 'sometimes',
            'rating' => 'sometimes',
            'video' => 'sometimes|file|mimes:mp4|max:200048',
            'price' => 'sometimes',
            'service_date' => 'sometimes',
            'service_time_from' => 'sometimes',
            'service_time_to' => 'sometimes',
            'gender'=>'sometimes'

           

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([

            'success'   => false,
            'message'   => 'Validation errors',
            'data'  => $validator->errors(),
      ],500));
    }
}
