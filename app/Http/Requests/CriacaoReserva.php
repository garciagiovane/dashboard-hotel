<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriacaoReserva extends FormRequest
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
            'servico' => 'nullable',
            'dias' => 'required|numeric|min:1',
            'quartoId' => 'required|numeric|min:1',
            'cpf' => 'required|string|size:11'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute é obrigatório',
            'numeric' => ':attribute deve ser numérico',
            'min' => 'Valor mínimo para :attribute é 1',
            'cpf.size' => 'CPF deve ter 11 dígitos'
        ];
    }
}
