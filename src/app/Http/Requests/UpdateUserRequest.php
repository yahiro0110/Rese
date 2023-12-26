<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zAZ]{2,4}$/', 'max:254'],
            'roles' => ['required', 'array'],
            'roles.*' => ['boolean'],
            'roles' => function ($attribute, $value, $fail) {
                if (!(in_array(true, $value, true))) {
                    $fail('少なくとも1つの役割を選択してください');
                }
            },
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '名前を入力してください',
            'name.string' => '名前は文字列で入力してください',
            'name.max' => '名前は60文字以内で入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',
            'email.regex' => 'メールアドレスの形式で入力してください',
            'email.max' => 'メールアドレスは254文字以内で入力してください',
        ];
    }
}
