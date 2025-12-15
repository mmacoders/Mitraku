<x-mail::message>
# Halo {{ $userName }},

Pengajuan MoU Anda dengan judul **"{{ $mouTitle }}"** telah diperbarui statusnya menjadi:

<x-mail::panel>
**{{ ucfirst($status) }}**
</x-mail::panel>

@if($status === 'disetujui' && $validityStart && $validityEnd)
Masa berlaku MoU:
**{{ \Carbon\Carbon::parse($validityStart)->translatedFormat('d F Y') }}** s/d **{{ \Carbon\Carbon::parse($validityEnd)->translatedFormat('d F Y') }}**

Silakan login ke aplikasi untuk melihat detail dan melanjutkan proses pengajuan PKS.
@elseif($status === 'ditolak')
Mohon maaf, pengajuan Anda belum dapat disetujui saat ini. Silakan hubungi admin untuk informasi lebih lanjut.
@endif

<x-mail::button :url="route('mitra.dashboard')">
Masuk ke Dashboard
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
