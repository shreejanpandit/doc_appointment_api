<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
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
            'start_time' =>  ['required', 'date_format:H:i'],
            'end_time' =>  ['required', 'date_format:H:i'],
            'week_day' => ['required', Rule::in(['sunday','monday','tuesday','wednesday','thursday','friday','saturday'])]
        ];
    }
}
