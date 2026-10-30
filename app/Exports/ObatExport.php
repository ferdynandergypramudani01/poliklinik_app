<?php

namespace App\Exports;

use App\Models\Obat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ObatExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Obat::all();
    }

    public function headings(): array
    {
        return [
            'Nama Obat',
            'Kemasan',
            'Harga',
        ];
    }

    public function map($obat): array
    {
        return [
            $obat->nama_obat,
            $obat->kemasan ?? '-',
            $obat->harga,
        ];
    }
}
