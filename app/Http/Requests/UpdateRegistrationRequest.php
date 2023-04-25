<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'register_date' => 'required|date',
            'attendance_confirm_date' => 'date|nullable',
            'registration_confirm_date' => 'date|nullable',
            'canceled_at' => 'date|nullable',
            'arrived_at' => 'date|nullable',
            'departed_at' => 'date|nullable',
            'event_id' => 'required|integer|min:0',
            'status_id' => 'required|integer|min:1',
            'room_id' => 'required|integer|min:0',
            'deposit' => 'required|numeric|min:0|max:10000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [];
    }
}
