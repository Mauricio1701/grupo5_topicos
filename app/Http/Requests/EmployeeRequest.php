<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeeRequest extends FormRequest
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
        $employeeId = $this->route('employee') ? $this->route('employee')->id : null;
        
        return [
            // DNI - 8 dígitos, único, formato peruano
            'dni' => [
                'required',
                'string',
                'regex:/^[0-9]{8}$/',
                Rule::unique('employees', 'dni')->ignore($employeeId),
            ],
            
            // Nombres y apellidos
            'names' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/', // Solo letras y espacios
            ],
            'lastnames' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/', // Solo letras y espacios
            ],
            
            // Fecha de nacimiento - debe ser mayor de edad (18 años)
            'birthday' => [
                'required',
                'date',
                'before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
                'after:' . Carbon::now()->subYears(65)->format('Y-m-d'), // Máximo 65 años
            ],
            
            // Licencia de conducir - requerida solo para conductores
            'license' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z]{1}[0-9]{8}$/', // Formato: A12345678
                Rule::unique('employees', 'license')->ignore($employeeId),
            ],
            
            // Dirección
            'address' => [
                'required',
                'string',
                'max:200',
                'min:10', // Dirección mínima
            ],
            
            // Email - formato válido, único
            'email' => [
                'nullable',
                'email:filter',
                'max:100',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            
            // Teléfono - formato peruano
            'phone' => [
                'nullable',
                'string',
                'regex:/^(\+51|51)?[9][0-9]{8}$/', // Formato peruano: +51987654321 o 987654321
            ],
            
            // Estado
            'status' => 'boolean',
            
            // Contraseña
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', // Al menos 1 minúscula, 1 mayúscula, 1 número
            ],
            
            // Tipo de empleado
            'type_id' => [
                'required',
                'exists:employeetype,id',
            ],
            
            // Foto
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048', // 2MB máximo
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // DNI
            'dni.required' => 'El DNI es obligatorio.',
            'dni.regex' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'Este DNI ya está registrado.',
            
            // Nombres
            'names.required' => 'Los nombres son obligatorios.',
            'names.regex' => 'Los nombres solo pueden contener letras y espacios.',
            'names.max' => 'Los nombres no pueden exceder 100 caracteres.',
            
            // Apellidos
            'lastnames.required' => 'Los apellidos son obligatorios.',
            'lastnames.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'lastnames.max' => 'Los apellidos no pueden exceder 200 caracteres.',
            
            // Fecha de nacimiento
            'birthday.required' => 'La fecha de nacimiento es obligatoria.',
            'birthday.before' => 'El empleado debe ser mayor de 18 años.',
            'birthday.after' => 'El empleado no puede ser mayor de 65 años.',
            
            // Licencia
            'license.regex' => 'La licencia debe tener el formato: A12345678.',
            'license.unique' => 'Esta licencia ya está registrada.',
            
            // Dirección
            'address.required' => 'La dirección es obligatoria.',
            'address.min' => 'La dirección debe tener al menos 10 caracteres.',
            'address.max' => 'La dirección no puede exceder 200 caracteres.',
            
            // Email
            'email.email' => 'El formato del email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            
            // Teléfono
            'phone.regex' => 'El teléfono debe tener formato peruano (987654321 o +51987654321).',
            
            // Contraseña
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos 1 mayúscula, 1 minúscula y 1 número.',
            
            // Tipo
            'type_id.required' => 'El tipo de empleado es obligatorio.',
            'type_id.exists' => 'El tipo de empleado seleccionado no es válido.',
            
            // Foto
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser formato JPG, JPEG o PNG.',
            'photo.max' => 'La imagen no puede ser mayor a 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y formatear datos antes de validar
        if ($this->dni) {
            $this->merge([
                'dni' => preg_replace('/[^0-9]/', '', $this->dni), // Solo números
            ]);
        }

        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^+0-9]/', '', $this->phone), // Solo números y +
            ]);
        }

        if ($this->names) {
            $this->merge([
                'names' => ucwords(strtolower(trim($this->names))), // Primera letra mayúscula
            ]);
        }

        if ($this->lastnames) {
            $this->merge([
                'lastnames' => ucwords(strtolower(trim($this->lastnames))), // Primera letra mayúscula
            ]);
        }
    }
}