<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
class ListarPokesOpcionesRequest extends FormRequest
{
        /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
         "nombre"=>"string",
         "pokedex"=>"integer|exists:pokemon,pokedex_number",
         "tipo"=>"string|exists:tipo_pokemon,tip_nombre",
         "region"=>"string|exists:regions,reg_nombre"
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es requerido',
            'integer' => 'El campo :attribute debe ser un número entero',
            'numeric' => 'El campo :attribute debe ser un número',
            'exists' => 'El :attribute debe existir en nuestro sistema',
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors()->all(), Response::HTTP_BAD_REQUEST)
        );
    }
}