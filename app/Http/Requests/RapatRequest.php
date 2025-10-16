<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RapatRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_waktu' => 'required|date_format:Y-m-d\TH:i', // Changed to accept datetime-local format
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:akan_datang,selesai,dibatalkan',
            'pks_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // 2MB max
            'shouldRemoveExistingDocument' => 'nullable|boolean'
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
            'judul.required' => 'Judul rapat harus diisi.',
            'tanggal_waktu.required' => 'Tanggal dan waktu rapat harus diisi.',
            'tanggal_waktu.date_format' => 'Format tanggal dan waktu tidak valid. Gunakan format YYYY-MM-DD HH:MM', // Updated message
            'status.required' => 'Status rapat harus dipilih.',
            'status.in' => 'Status rapat tidak valid.',
            'pks_document.file' => 'File dokumen PKS tidak valid.',
            'pks_document.mimes' => 'File dokumen PKS harus berupa PDF, DOC, atau DOCX.',
            'pks_document.max' => 'Ukuran file dokumen PKS tidak boleh lebih dari 2MB.',
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // If tanggal_waktu is in datetime-local format, we can use it as is
        // Laravel's date_format rule will validate it properly
        $this->merge([
            // No transformation needed as we're now accepting the datetime-local format directly
        ]);
    }
}