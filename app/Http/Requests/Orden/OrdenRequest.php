<?php

namespace App\Http\Requests\Orden;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class OrdenRequest extends FormRequest
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
            'fechaHoraSolicitud' => 'required|date_format:Y-m-d H:i:s',
            'fechaHoraLlegada' => 'nullable|date_format:Y-m-d H:i:s',
            'fechaHoraSalida' => 'nullable|date_format:Y-m-d H:i:s',
            'persona_solicitante' => 'required|string|max:255',
            'puesto' => 'nullable|string|max:255',
            'direccion' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clientes,id',
            'tecnico_id' => 'required|exists:users,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.cantidad' => 'required|string|min:1',
            'detalles.*.descripcion' => 'required|string|min:3',
            'detalles.*.producto_id' => 'required|integer|exists:productos,id',
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
            '*.array' => 'El :attribute debe ser un arreglo',
            '*.exists' => ':attribute no existe',
            'detalles.*.cantidad.required' => 'La :attribute es requerida.',
            'detalles.*.cantidad.integer' => 'La :attribute debe ser un entero.',
            'detalles.*.cantidad.min' => 'La :attribute debe tener al menos :min caracteres.',
            'detalles.*.descripcion.required' => 'La :attribute es requerida.',
            'detalles.*.descripcion.string' => 'La :attribute debe ser una cadena de texto.',
            'detalles.*.descripcion.min' => 'La :attribute debe tener al menos :min caracteres.',
            'detalles.*.producto_id.required' => 'El :attribute es requerido.',
            'detalles.*.producto_id.integer' => 'El :attribute debe ser un número entero.',
            'detalles.*.producto_id.exists' => 'El :attribute seleccionado del detalle no existe.',

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
            'fechaHoraSolicitud' => 'fecha y hora de solicitud',
            'fechaHoraLlegada' => 'fecha y hora de llegada',
            'fechaHoraSalida' => 'fecha y hora de salida',
            'puesto' => 'puesto',
            'persona_solicitante' => 'persona solicitante',            
            'direccion' => 'dirección',
            'estatus' => 'estatus',
            'cliente_id' => 'cliente',
            'tecnico_id' => 'usuario',
            'sucursal_id' => 'sucursal',
            'detalles' => 'detalles de la orden',
            'detalles.*.cantidad' => ' cantidad del detalle',
            'detalles.*.descripcion' => ' descripción del detalle',
            'detalles.*.producto_id' => ' producto del detalle',
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
