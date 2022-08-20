<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnterpriseRequest extends FormRequest
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
            'name' => 'required',
            'document' => 'required',
            'phone' => 'required',
            'email' => '',
            'address' => ''
        ];
    }

    public function messages()
    {
        return [
          'name'  => 'O campo nome é obrigatório.',
          'document' => 'O CNPJ e obigatório',
          'phone' => 'O telefone e obigatório',
        ];
    }
}
