<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PksSubmissionRequest extends FormRequest
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
            'partner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'kak_document' => 'required|file|mimes:pdf,docx|max:2048',
            'mou_document' => 'required|file|mimes:pdf,docx|max:2048',
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
            'title.required' => 'Judul harus diisi.',
            'purpose.required' => 'Tujuan pengajuan harus diisi.',
            'kak_document.required' => 'Dokumen KAK harus diunggah.',
            'kak_document.file' => 'File dokumen KAK tidak valid.',
            'kak_document.mimes' => 'File dokumen KAK harus berupa PDF atau DOCX.',
            'kak_document.max' => 'Ukuran file dokumen KAK tidak boleh lebih dari 2MB.',
            'mou_document.required' => 'Dokumen MoU harus diunggah.',
            'mou_document.file' => 'File dokumen MoU tidak valid.',
            'mou_document.mimes' => 'File dokumen MoU harus berupa PDF atau DOCX.',
            'mou_document.max' => 'Ukuran file dokumen MoU tidak boleh lebih dari 2MB.',
        ];
    }
}