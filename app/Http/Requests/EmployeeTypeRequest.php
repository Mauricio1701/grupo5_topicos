<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeTypeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $employeeTypeId = $this->route('employee_type') ? $this->route('employee_type')->id : null;
        
        return [
            // Nombre único, requerido
            'name' => [
                'required',
                'string',
                'max:100',
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/', // Solo letras y espacios
                Rule::unique('employeetype', 'name')->ignore($employeeTypeId),
            ],
            
            // Descripción opcional
            'description' => [
                'nullable',
                'string',
                'max:500',
                'min:10', // Mínimo 10 caracteres si se proporciona
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Nombre
            'name.required' => 'El nombre del tipo de empleado es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder 100 caracteres.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'name.unique' => 'Ya existe un tipo de empleado con este nombre.',
            
            // Descripción
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no puede exceder 500 caracteres.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre del tipo de empleado',
            'description' => 'descripción',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y formatear datos antes de validar
        if ($this->name) {
            $this->merge([
                'name' => ucwords(strtolower(trim($this->name))), // Primera letra mayúscula
            ]);
        }

        if ($this->description) {
            $this->merge([
                'description' => trim($this->description),
            ]);
        }
    }
}