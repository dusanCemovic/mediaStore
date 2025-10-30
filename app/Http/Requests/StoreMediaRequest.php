<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware; allow validation.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // setup rules for input files
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:511',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,mkv,webm|max:78848', // max 77MB L.D. :)
        ];
    }
}
