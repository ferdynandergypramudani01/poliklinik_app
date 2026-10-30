<?php

namespace App\Exports;

use App\Models\JadwalPeriksa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JadwalPeriksaExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return JadwalPeriksa::where('id_dokter', auth()->id())
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
        ];
    }

    public function map($jadwal): array
    {
        return [
            $jadwal->hari,
            $jadwal->jam_mulai,
            $jadwal->jam_selesai,
        ];
    }
}
