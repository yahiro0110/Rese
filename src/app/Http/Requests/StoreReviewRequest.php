<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:50'],
            'comment' => ['required', 'string', 'max:400'],
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,', 'max:5000'],
        ];
    }

    public function messages()
    {
        return [
            'rating.integer' => '評価は数値で入力してください',
            'rating.between' => '評価は1〜5の範囲で入力してください',
            'title.required' => 'タイトルは必須です',
            'title.string' => 'タイトルは文字列で入力してください',
            'title.max' => 'タイトルは50文字以内で入力してください',
            'comment.required' => '内容は必須です',
            'comment.string' => '内容は文字列で入力してください',
            'comment.max' => '内容は400文字以内で入力してください',
            'file.image' => '画像ファイルを選択してください',
            'file.mimes' => '画像ファイルはjpeg,png,jpg形式で選択してください',
            'file.max' => '画像ファイルは5MB以内で選択してください',
        ];
    }
}
