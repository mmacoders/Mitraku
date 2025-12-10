<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kerja Sama Daerah Kota Gorontalo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: top;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .no-border {
            border: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KERJA SAMA DAERAH KOTA GORONTALO TAHUN {{ date('Y') }}</h1>
        <h1>YANG MASIH BERLAKU</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Bentuk Kerja Sama dan Para Pihak</th>
                <th width="10%">Nomor</th>
                <th width="15%">Tanggal dan Masa Berlaku</th>
                <th width="20%">Maksud dan Tujuan</th>
                <th width="10%">Jangka Waktu</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $index => $submission)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $submission->title }}</strong><br>
                        <br>
                        Pihak 1: Pemerintah Kota Gorontalo<br>
                        Pihak 2: {{ $submission->user->name }}
                    </td>
                    <td>-</td> <!-- Nomor PKS placeholder -->
                    <td>
                        @if($submission->validity_period_start && $submission->validity_period_end)
                            {{ \Carbon\Carbon::parse($submission->validity_period_start)->translatedFormat('d F Y') }} <br> s.d <br>
                            {{ \Carbon\Carbon::parse($submission->validity_period_end)->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $submission->description }}</td>
                    <td class="text-center">
                        @if($submission->validity_period_start && $submission->validity_period_end)
                            @php
                                $start = \Carbon\Carbon::parse($submission->validity_period_start);
                                $end = \Carbon\Carbon::parse($submission->validity_period_end);
                                $diff = $start->diff($end);
                                $duration = [];
                                if ($diff->y > 0) $duration[] = $diff->y . ' Tahun';
                                if ($diff->m > 0) $duration[] = $diff->m . ' Bulan';
                                if (empty($duration)) $duration[] = $diff->d . ' Hari';
                            @endphp
                            {{ implode(' ', $duration) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($submission->validity_period_end)
                            @if(\Carbon\Carbon::parse($submission->validity_period_end)->isPast())
                                Habis Masa Berlaku
                            @else
                                Masih Berlaku
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            @if(count($submissions) == 0)
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
