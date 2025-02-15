<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PutBookingsRequest extends FormRequest
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
            'start_at' => 'required|date|date_format:Y-m-d|after:tomorrow',
            'end_at' => 'required|date|date_format:Y-m-d|after:start_at'
        ];
    }
}
