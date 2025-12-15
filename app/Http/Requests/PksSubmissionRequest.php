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
        $isExistingPks = $this->input('is_existing_pks', false);
        
        $rules = [
            'partner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'user_id' => $isExistingPks ? 'required|exists:users,id' : 'nullable',
            'validity_period_start' => $isExistingPks ? 'required|date' : 'nullable',
            'validity_period_end' => $isExistingPks ? 'required|date|after:validity_period_start' : 'nullable',
            'status' => 'sometimes|in:disetujui',
        ];
        
        // For new submissions, documents are required
        // For existing PKS added by admin, documents are optional
        if (!$isExistingPks) {
            $rules['kak_document'] = 'nullable|file|mimes:pdf,docx|max:2048';
            $rules['mou_document'] = 'required|file|mimes:pdf,docx|max:2048';
            $rules['mou_id'] = 'required|exists:mous,id';
        } else {
            // For existing PKS, documents are optional
            $rules['kak_document'] = 'nullable|file|mimes:pdf,docx|max:2048';
            $rules['mou_document'] = 'nullable|file|mimes:pdf,docx|max:2048';
            $rules['mou_id'] = 'nullable|exists:mous,id';
        }
        
        if ($this->isMethod('put') || $this->isMethod('patch')) {
             $rules = [
                'title' => 'required|string|max:255',
                'purpose' => 'required|string',
                'kak_document' => 'nullable|file|mimes:pdf,docx|max:2048',
                'mou_document' => 'nullable|file|mimes:pdf,docx|max:2048',
                'status' => 'nullable', 
            ];
            return $rules;
        }

        return $rules;
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
            'user_id.required' => 'Nama mitra harus dipilih.',
            'user_id.exists' => 'Mitra yang dipilih tidak valid.',
            'validity_period_start.required' => 'Tanggal mulai berlaku harus diisi.',
            'validity_period_end.required' => 'Tanggal akhir berlaku harus diisi.',
            'validity_period_end.after' => 'Tanggal akhir harus setelah tanggal mulai.',
            'status.in' => 'Status tidak valid untuk PKS yang sudah berjalan.',
            'mou_id.required' => 'Dasar MoU harus dipilih.',
            'mou_id.exists' => 'MoU yang dipilih tidak valid.',
        ];
    }
}