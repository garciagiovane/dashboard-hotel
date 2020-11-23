<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriacaoQuarto extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|string|max:10',
            'andar' => 'required|numeric|min:0|max:10',
            'valorDiaria' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute é obrigatório',
            'numeric' => ':attribute precisa ser um número inteiro',
            'min' => 'Valor do campo :attribute deve ser maior que 0',
            'max' => 'O :attribute deve ser no máximo 10',
            'status.max' => 'Status não deve ultrapassar 10 caracteres'
        ];
    }
}
