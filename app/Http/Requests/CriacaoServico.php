<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriacaoServico extends FormRequest
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
            'descricao' => 'required|max:49',
            'valorUnitario' => 'required|numeric|min:1',
            'status' => 'required|string|max:10'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute é obrigatório',
            'max' => ':attribute não deve ultrapassar 49 caracteres',
            'min' => 'Mínimo para :attribute é 1',
            'numeric' => ':attribute deve ser numérico',
            'status.max' => 'Status não deve ultrapassar 10 caracteres'
        ];
    }
}
