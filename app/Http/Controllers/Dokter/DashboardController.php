<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil seluruh jadwal praktik milik dokter yang sedang login.
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::id())
            ->with([
                'daftarPolis' => function ($query) {
                    $query->whereIn('status_antrian', [
                        'menunggu',
                        'dipanggil',
                        'diperiksa',
                    ])
                    ->with('pasien')
                    ->orderBy('no_antrian');
                },
            ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('dokter.dashboard', compact('jadwals'));
    }
}
