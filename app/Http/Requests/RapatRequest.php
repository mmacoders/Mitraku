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
        // Only allow authenticated users to make this request
        return auth()->check();
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
            'tanggal_waktu' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:akan_datang,selesai,dibatalkan',
            'pks_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // 2MB max
            'shouldRemoveExistingDocument' => 'nullable|boolean',
            'pks_submission_id' => 'required|integer|exists:pks_submissions,id',
            'invited_mitra' => 'nullable|array',
            'invited_mitra.*' => 'integer|exists:users,id',
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
            'status.required' => 'Status rapat harus dipilih.',
            'status.in' => 'Status rapat tidak valid.',
            'pks_document.file' => 'File dokumen PKS tidak valid.',
            'pks_document.mimes' => 'File dokumen PKS harus berupa PDF, DOC, atau DOCX.',
            'pks_document.max' => 'Ukuran file dokumen PKS tidak boleh lebih dari 2MB.',
            'pks_submission_id.required' => 'PKS terkait harus dipilih.',
            'pks_submission_id.integer' => 'ID pengajuan PKS tidak valid.',
            'pks_submission_id.exists' => 'Pengajuan PKS tidak ditemukan.',
            'invited_mitra.array' => 'Daftar mitra yang diundang tidak valid.',
            'invited_mitra.*.integer' => 'ID mitra tidak valid.',
            'invited_mitra.*.exists' => 'Mitra tidak ditemukan.',
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Trim whitespace from important fields
        $this->merge([
            'judul' => trim($this->judul ?? ''),
            'tanggal_waktu' => trim($this->tanggal_waktu ?? ''),
            'status' => trim($this->status ?? ''),
            'lokasi' => trim($this->lokasi ?? ''),
            'deskripsi' => trim($this->deskripsi ?? ''),
        ]);
        
        // Handle pks_submission_id
        if ($this->pks_submission_id === '') {
            $this->merge(['pks_submission_id' => null]);
        }
        
        // Handle invited_mitra if it's not an array
        if ($this->invited_mitra && !is_array($this->invited_mitra)) {
            $this->merge(['invited_mitra' => []]);
        }
        
        // Ensure required fields are not null
        if (is_null($this->judul)) {
            $this->merge(['judul' => '']);
        }
        
        if (is_null($this->tanggal_waktu)) {
            $this->merge(['tanggal_waktu' => '']);
        }
        
        if (is_null($this->status)) {
            $this->merge(['status' => '']);
        }
    }
}