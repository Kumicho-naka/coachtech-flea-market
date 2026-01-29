<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '評価を選択してください。',
            'rating.integer' => '評価は1から5の整数で選択してください。',
            'rating.min' => '評価は1から5で選択してください。',
            'rating.max' => '評価は1から5で選択してください。',
        ];
    }
}
