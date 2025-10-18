<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は20文字以内で入力してください',
            'profile_image.image' => 'プロフィール画像は画像ファイルである必要があります',
            'profile_image.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はXXX-XXXX形式で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
