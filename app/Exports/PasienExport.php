<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PasienExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return User::where('role', 'pasien')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pasien',
            'Email',
            'No. KTP',
            'No. HP',
            'Alamat',
        ];
    }

    public function map($pasien): array
    {
        return [
            $pasien->name,
            $pasien->email,
            $pasien->no_ktp ?? '-',
            $pasien->no_hp ?? '-',
            $pasien->alamat ?? '-',
        ];
    }
}
