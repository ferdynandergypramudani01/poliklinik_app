<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PoliController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran poli.
     */
    public function get()
    {
        $user = Auth::user();

        $polis = Poli::all();

        $jadwal = JadwalPeriksa::with([
            'dokter',
            'dokter.poli',
        ])->get();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwal,
        ]);
    }

    /**
     * Memproses pendaftaran pasien ke poli.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'id_poli' => [
                'required',
                'integer',
                'exists:poli,id',
            ],
            'id_jadwal' => [
                'required',
                'integer',
                'exists:jadwal_periksa,id',
            ],
            'keluhan' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $daftarPoli = DB::transaction(function () use ($validated) {

            // Kunci data pasien agar pendaftaran bersamaan
            // dari akun yang sama tidak terjadi.
            $pasien = User::whereKey(Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            // Kunci jadwal agar nomor antrian tidak duplikat.
            $jadwal = JadwalPeriksa::whereKey($validated['id_jadwal'])
                ->lockForUpdate()
                ->first();

            if (! $jadwal) {
                throw ValidationException::withMessages([
                    'id_jadwal' => 'Jadwal pemeriksaan tidak ditemukan.',
                ]);
            }

            // Pastikan jadwal dokter sesuai dengan poli pilihan.
            $dokter = $jadwal->dokter;

            if (
                ! $dokter ||
                (int) $dokter->id_poli !== (int) $validated['id_poli']
            ) {
                throw ValidationException::withMessages([
                    'id_jadwal' => 'Jadwal dokter tidak sesuai dengan poli yang dipilih.',
                ]);
            }

            // Periksa apakah pasien masih memiliki antrian aktif.
            $sudahMemilikiAntrian = DaftarPoli::where(
                'id_pasien',
                $pasien->id
            )
                ->whereIn('status_antrian', [
                    'menunggu',
                    'dipanggil',
                    'diperiksa',
                ])
                ->exists();

            if ($sudahMemilikiAntrian) {
                throw ValidationException::withMessages([
                    'id_jadwal' => 'Anda masih memiliki antrian aktif. Selesaikan pemeriksaan sebelumnya sebelum mendaftar kembali.',
                ]);
            }

            // Ambil nomor antrian terakhir pada jadwal tersebut.
            $nomorTerakhir = DaftarPoli::where(
                'id_jadwal',
                $jadwal->id
            )->max('no_antrian');

            $nomorAntrian = ($nomorTerakhir ?? 0) + 1;

            // Simpan pendaftaran baru.
            return DaftarPoli::create([
                'id_pasien' => $pasien->id,
                'id_jadwal' => $jadwal->id,
                'keluhan' => $validated['keluhan'],
                'no_antrian' => $nomorAntrian,
                'status_antrian' => 'menunggu',
                'waktu_dipanggil' => null,
            ]);
        });

        return redirect()
            ->back()
            ->with('message', 'Berhasil mendaftar ke poli. Nomor antrian Anda: '.$daftarPoli->no_antrian)
            ->with('type', 'success');
    }
}
