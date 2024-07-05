<?php

namespace App\Http\Requests\Visita;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class VisitaRequest extends FormRequest
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
            'motivo' => 'required|string|min:3|regex:/^[a-zA-Z\s]*$/',
            'fechaHoraSolicitud' => 'required|date_format:Y-m-d H:i:s',
            'fechaHoraLlegada' => 'nullable|date_format:Y-m-d H:i:s',
            'fechaHoraSalida' => 'nullable|date_format:Y-m-d H:i:s',
            'direccion' => 'required|string|min:10',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'tecnico_id' => 'required|integer|exists:users,id',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
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
            '*.regex' => 'El :attribute solo puede contener letras y espacios',
            '*.date_format' => 'El :attribute debe tener el formato Y-m-d H:i:s',
            '*.exists' => ':attribute no existe',
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
            'motivo' => 'motivo',
            'fechaHoraSolicitud' => 'fecha y hora de solicitud',
            'fechaHoraLlegada' => 'fecha y hora de llegada',
            'fechaHoraSalida' => 'fecha y hora de salida',
            'direccion' => 'dirección',
            'estatus' => 'estatus',
            'cliente_id' => 'Cliente',
            'tecnico_id' => 'Usuario',
            'sucursal_id' => 'Sucursal',
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
