<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'price.required' => '価格を入力してください',
            'price.min' => '価格は0円以上で入力してください',
            'condition_id.required' => '商品の状態を選択してください',
            'categories.required' => 'カテゴリを選択してください',
            'image.required' => '商品画像をアップロードしてください',
            'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',
        ];
    }
}
