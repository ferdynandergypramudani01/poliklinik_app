<?php

namespace App\Exports;

use App\Models\Periksa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiwayatPasienExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) {
            $query->where('id_dokter', Auth::id());
        })
            ->with([
                'daftarPoli.pasien',
            ])
            ->orderBy('tgl_periksa', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Antrian',
            'Nama Pasien',
            'Keluhan',
            'Tanggal Periksa',
            'Biaya',
        ];
    }

    public function map($riwayat): array
    {
        return [
            $riwayat->daftarPoli->no_antrian,
            $riwayat->daftarPoli->pasien->nama,
            $riwayat->daftarPoli->keluhan,
            Carbon::parse($riwayat->tgl_periksa)->format('d/m/Y'),
            $riwayat->biaya_periksa,
        ];
    }
}
