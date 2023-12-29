<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
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
            'genre_id' => ['required'],
            'name' => ['required', 'string', 'max:60'],
            'tel' => ['required', 'string', 'regex:/^[0-9-]+$/', 'max:21'],
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zAZ]{2,4}$/', 'max:254'],
            'postal' => ['required', 'string', 'regex:/^\d{7}$/'],
            'address' => ['required', 'string', 'max:161'],
            'description' => ['required', 'string', 'max:125'],
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,', 'max:5000'],
        ];
    }

    public function messages()
    {
        return [
            'genre_id.required' => 'ジャンルは必須です',

            'name.required' => '名前は必須です',
            'name.string' => '名前は文字列で入力してください',
            'name.max' => '名前は60文字以内で入力してください',

            'tel.required' => '電話番号は必須です',
            'tel.string' => '電話番号は文字列で入力してください',
            'tel.regex' => '電話番号は数字とハイフンのみで入力してください',
            'tel.max' => '電話番号は21文字以内で入力してください',

            'email.required' => 'メールアドレスは必須です',
            'email.email' => '有効なメールアドレスを入力してください',
            'email.regex' => 'メールアドレスの形式が正しくありません',
            'email.max' => 'メールアドレスは254文字以内で入力してください',

            'postal.required' => '郵便番号は必須です',
            'postal.string' => '郵便番号は文字列で入力してください',
            'postal.regex' => '郵便番号は7桁の数字である必要があります',

            'address.required' => '住所は必須です',
            'address.string' => '住所は文字列で入力してください',
            'address.max' => '住所は161文字以内で入力してください',

            'description.required' => '説明は必須です',
            'description.string' => '説明は文字列で入力してください',
            'description.max' => '説明は125文字以内で入力してください',

            'file.image' => 'ファイルは画像である必要があります',
            'file.mimes' => '画像は jpeg, png, jpg 形式である必要があります',
            'file.max' => '画像のサイズは5MB以下にしてください',
        ];
    }
}
