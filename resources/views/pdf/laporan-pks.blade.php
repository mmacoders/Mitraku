<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kerja Sama Daerah Kota Gorontalo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* Adjusted for more columns */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px; /* Increased padding for neatness */
            vertical-align: top;
            text-align: left;
            word-wrap: break-word; /* Ensure text wraps */
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KERJA SAMA DAERAH KOTA GORONTALO TAHUN 2023</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Nomor Dokumen Perjanjian</th>
                <th width="14%">Mitra Kerja Sama</th>
                <th width="10%">Jenis Dokumen Perjanjian</th>
                <th width="18%">Judul Dokumen</th>
                <th width="10%">Tanggal Penetapan</th>
                <th width="12%">Urusan Pemerintahan yang Dikerjasamakan</th>
                <th width="10%">Jangka Waktu Pelaksanaan</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data Real (Dynamic) -->
            @foreach($submissions as $index => $submission)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>-</td> <!-- Nomor Dokumen belum tersedia di database -->
                    <td>{{ $submission->user->name ?? '-' }}<br><small>{{ $submission->user->company ?? '' }}</small></td>
                    <td>Perjanjian Kerja Sama (PKS)</td> <!-- Default type -->
                    <td>{{ $submission->title }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y') }}
                    </td>
                    <td>
                        {{ $submission->description ?? $submission->purpose ?? '-' }}
                    </td>
                    <td class="text-center">
                        @if($submission->validity_period_start && $submission->validity_period_end)
                            {{ \Carbon\Carbon::parse($submission->validity_period_start)->translatedFormat('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($submission->validity_period_end)->translatedFormat('d M Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($submission->status == 'disetujui')
                            Berlaku
                        @elseif($submission->status == 'ditolak')
                            Tidak Berlaku
                        @else
                            {{ ucfirst($submission->status) }}
                        @endif
                    </td>
                </tr>
            @endforeach

            <!-- Data Contoh (Dummy) - Ditambahkan Sesuai Permintaan -->
            @if(count($submissions) == 0 || true) <!-- Always show dummy data as requested for preview -->
                @php
                    $dummyStartIndex = count($submissions) + 1;
                @endphp
                <tr>
                    <td class="text-center">{{ $dummyStartIndex }}</td>
                    <td>001/KS/2023</td>
                    <td>Universitas Negeri Gorontalo</td>
                    <td>Nota Kesepakatan</td>
                    <td>Peningkatan Kualitas Sumber Daya Manusia melalui Pendidikan dan Pelatihan</td>
                    <td class="text-center">5 Januari 2023</td>
                    <td>Pendidikan</td>
                    <td class="text-center">5 Tahun (2023-2028)</td>
                    <td class="text-center">Berlaku</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $dummyStartIndex + 1 }}</td>
                    <td>002/PKS/Dinkes/2023</td>
                    <td>BPJS Kesehatan Cabang Gorontalo</td>
                    <td>Perjanjian Kerja Sama</td>
                    <td>Optimalisasi Penyelenggaraan Jaminan Kesehatan Nasional bagi Penduduk Kota Gorontalo</td>
                    <td class="text-center">12 Februari 2023</td>
                    <td>Kesehatan</td>
                    <td class="text-center">1 Tahun (2023-2024)</td>
                    <td class="text-center">Berlaku s/d 12 Feb 2024</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $dummyStartIndex + 2 }}</td>
                    <td>003/KB/2023</td>
                    <td>PT. Telkom Indonesia Tbk</td>
                    <td>Kesepakatan Bersama</td>
                    <td>Pengembangan Implementasi Smart City di Lingkungan Pemerintah Kota Gorontalo</td>
                    <td class="text-center">20 Maret 2023</td>
                    <td>Komunikasi dan Informatika</td>
                    <td class="text-center">3 Tahun (2023-2026)</td>
                    <td class="text-center">Berlaku</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $dummyStartIndex + 3 }}</td>
                    <td>004/PKS/DLH/2023</td>
                    <td>Bank Sampah Induk Gorontalo</td>
                    <td>Perjanjian Kerja Sama</td>
                    <td>Pengelolaan Sampah Terpadu Berbasis Pemberdayaan Masyarakat</td>
                    <td class="text-center">10 April 2023</td>
                    <td>Lingkungan Hidup</td>
                    <td class="text-center">2 Tahun (2023-2025)</td>
                    <td class="text-center">Berlaku</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $dummyStartIndex + 4 }}</td>
                    <td>005/MoU/2023</td>
                    <td>Kejaksaan Negeri Gorontalo</td>
                    <td>Nota Kesepahaman</td>
                    <td>Penanganan Masalah Hukum Bidang Perdata dan Tata Usaha Negara</td>
                    <td class="text-center">15 Mei 2023</td>
                    <td>Hukum</td>
                    <td class="text-center">2 Tahun (2023-2025)</td>
                    <td class="text-center">Berlaku</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
