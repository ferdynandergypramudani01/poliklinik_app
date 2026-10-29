<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Http\Request;

class RiwayatPendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $riwayat = DaftarPoli::query()
            ->where('id_pasien', $request->user()->id)
            ->with([
                'jadwalPeriksa.dokter.poli',
                'periksa',
            ])
            ->latest('created_at')
            ->get();

        return view('pasien.riwayat-pendaftaran.index', compact('riwayat'));
    }

    public function show(Request $request, DaftarPoli $daftarPoli)
    {
        // Pastikan pendaftaran adalah milik pasien yang login.
        abort_unless(
            $daftarPoli->id_pasien === $request->user()->id,
            404
        );

        $daftarPoli->load([
            'jadwalPeriksa.dokter.poli',
            'periksa.detailPeriksas.obat',
        ]);

        // Halaman detail hanya boleh dibuka setelah pemeriksaan dilakukan.
        abort_unless($daftarPoli->periksa, 404);

        return view('pasien.riwayat-pendaftaran.show', compact('daftarPoli'));
    }
}
