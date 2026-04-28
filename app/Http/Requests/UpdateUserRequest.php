<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'id_karyawan' => ['required', 'string', 'max:50', Rule::unique('users', 'id_karyawan')->ignore($userId)],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:100'],
            'posisi_jabatan' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'no_telp' => ['required', 'string', 'max:20', Rule::unique('users', 'no_telp')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
