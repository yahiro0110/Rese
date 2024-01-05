<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i', 'after_or_equal:09:00', 'before_or_equal:23:00'],
            'members' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '日付は必須です',
            'date.date' => '日付は日付形式で入力してください',

            'time.required' => '時間は必須です',
            'time.date_format' => '時間は時間形式で入力してください',
            'time.after_or_equal' => '時間は09:00以降で入力してください',
            'time.before_or_equal' => '時間は23:00以前で入力してください',

            'members.required' => '人数は必須です',
            'members.integer' => '人数は整数で入力してください',
            'members.min' => '人数は1人以上で入力してください',
            'members.max' => '人数は20人以下で入力してください',
        ];
    }
}
