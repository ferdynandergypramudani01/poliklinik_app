<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil pendaftaran aktif milik pasien yang sedang login.
        $antrianAktif = DaftarPoli::where('id_pasien', Auth::id())
            ->whereIn('status_antrian', [
                'menunggu',
                'dipanggil',
                'diperiksa',
            ])
            ->with([
                'jadwalPeriksa.dokter',
                'jadwalPeriksa.dokter.poli',
            ])
            ->latest('id')
            ->first();

        $antrianSekarang = null;

        if ($antrianAktif) {
            // Prioritaskan antrean yang sedang dipanggil atau diperiksa.
            $antrianSekarang = DaftarPoli::where('id_jadwal', $antrianAktif->id_jadwal)
                ->whereIn('status_antrian', ['dipanggil', 'diperiksa'])
                ->orderBy('no_antrian')
                ->first();

            // Jika belum ada yang dipanggil, tampilkan antrean menunggu pertama.
            if (! $antrianSekarang) {
                $antrianSekarang = DaftarPoli::where('id_jadwal', $antrianAktif->id_jadwal)
                    ->where('status_antrian', 'menunggu')
                    ->orderBy('no_antrian')
                    ->first();
            }
        }

        return view('pasien.dashboard', compact(
            'antrianAktif',
            'antrianSekarang'
        ));
    }
}
