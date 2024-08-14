<?php

namespace App\Http\Requests\Sucursal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class SucursalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Validar esto según las reglas de negocio de la aplicación
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|min:3',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|min:10|max:10',
            'cliente_id' => 'required|integer'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

     public function messages(): array
     {
         return [
             '*.required' => 'El :attribute es requerido',
             '*.string' => 'El :attribute debe ser una cadena de texto',
             '*.min' => 'El :attribute debe tener al menos :min caracteres',
             '*.max' => 'El :attribute no puede tener más de :max caracteres',
             '*.integer' => 'El :attribute debe ser un número entero',
             '*.regex' => 'El :attribute solo puede contener letras',
         ];
     }

     /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'cliente_id' => 'cliente_id'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
