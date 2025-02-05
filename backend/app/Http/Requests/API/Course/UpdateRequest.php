<?php

namespace App\Http\Requests\API\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateRequest extends FormRequest
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
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s|after:start_date',
            'status' => 'nullable|in:active|active,not active',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'course_contents' => 'required|array|min:1',
            'course_contents.*.id' => 'required',
            'course_contents.*.description' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'start_date.required' => 'The start date field is required.',
            'start_date.date_format' => 'The start date format is invalid.',
            'end_date.required' => 'The end date field is required.',
            'end_date.date_format' => 'The end date format is invalid.',
            'end_date.after' => 'The end date must be after the start date.',
            'status.in' => 'The selected status is invalid.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'category.required' => 'The category field is required.',
            'category.string' => 'The category must be a string.',
            'category.max' => 'The category may not be greater than 255 characters.',
            'description.nullable' => 'The description field is optional.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a numeric value.',
            'price.min' => 'The price must be at least 0.',
            'course_contents.required' => 'The course contents field is required.',
            'course_contents.array' => 'The course contents must be an array.',
            'course_contents.*.id.required' => 'Each course content must have a id.',
            'course_contents.*.description.required' => 'Each course content must have a description.'
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
