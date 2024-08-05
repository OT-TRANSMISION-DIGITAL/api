<?php

namespace App\Http\Requests\Imagen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class FirmaRequest extends FormRequest
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
            'firma' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
             '*.required' => 'La :attribute es requerida',
             '*.image' => 'El archivo no es una imagen',
             '*.mimes' => 'La :attribute debe ser un archivo de tipo: jpeg, png, jpg',
             '*.max' => 'La :attribute no puede pesar más de :max kilobytes',
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
            'firma' => 'firma',
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
