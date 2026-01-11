<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title' => 'required|string|min:2',
            'body' => 'required|string|min:2',
        ];
    }

    public function messages()
    {
       return [
        'title.required' => 'Title is required',
        'title.string' => 'Title must be valid string',
        'title.min' => 'Title must be at least 2 characters',
       ];
    }
}
