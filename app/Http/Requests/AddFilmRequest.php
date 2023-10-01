<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddFilmRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'imdb' => ['required', 'regex:/^tt\d+$/', 'unique:films,imdb_id']
        ];
    }

    public function messages()
    {
        return [
            'imdb.regex' => 'imdb id должен быть передан в формате ttNNNN',
            'imdb.unique' => 'Такой фильм уже есть'
        ];
    }
}
