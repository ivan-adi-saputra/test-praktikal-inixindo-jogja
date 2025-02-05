<?php

namespace App\Http\Requests\API\Enrollment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateRequest extends FormRequest
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
            'participant_id' => 'required',
            'course_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'participant_id.required' => 'The participant id field is required.',
            'course_id.required' => 'The course id field is required.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => $validator->errors()->first(),
            'code' => 400,
        ]);

        throw new HttpResponseException($response);
    }
}
