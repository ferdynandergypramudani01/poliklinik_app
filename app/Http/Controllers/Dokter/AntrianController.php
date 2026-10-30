<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianDiperbarui;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AntrianController extends Controller
{
    /**
     * Memanggil pasien berikutnya berdasarkan jadwal dokter.
     */
    public function panggilBerikutnya($idJadwal)
    {
        $antrian = DB::transaction(function () use ($idJadwal) {

            // Pastikan jadwal merupakan milik dokter yang login.
            $jadwal = JadwalPeriksa::where('id', $idJadwal)
                ->where('id_dokter', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan tidak ada pasien yang sedang dipanggil
            // atau sedang menjalani pemeriksaan.
            $antrianAktif = DaftarPoli::where('id_jadwal', $jadwal->id)
                ->whereIn('status_antrian', ['dipanggil', 'diperiksa'])
                ->lockForUpdate()
                ->exists();

            if ($antrianAktif) {
                throw ValidationException::withMessages([
                    'antrian' => 'Selesaikan antrian yang sedang aktif terlebih dahulu.',
                ]);
            }

            // Ambil pasien dengan nomor terkecil yang masih menunggu.
            $antrian = DaftarPoli::where('id_jadwal', $jadwal->id)
                ->where('status_antrian', 'menunggu')
                ->orderBy('no_antrian')
                ->lockForUpdate()
                ->first();

            if (! $antrian) {
                throw ValidationException::withMessages([
                    'antrian' => 'Tidak ada pasien yang sedang menunggu.',
                ]);
            }

            // Ubah status menjadi dipanggil.
            $antrian->update([
                'status_antrian' => 'dipanggil',
                'waktu_dipanggil' => now(),
            ]);

            // Kirim event untuk memperbarui tampilan secara real-time.
            event(new AntrianDiperbarui($antrian));

            return $antrian;
        });

        return back()->with([
            'message' => 'Antrian nomor '.$antrian->no_antrian.' berhasil dipanggil.',
            'type' => 'success',
        ]);
    }

    /**
     * Memulai pemeriksaan pasien yang sudah dipanggil.
     */
    public function mulaiPemeriksaan($id)
    {
        $antrian = DB::transaction(function () use ($id) {

            // Pastikan antrian merupakan milik jadwal dokter yang login.
            $antrian = DaftarPoli::whereKey($id)
                ->whereHas('jadwalPeriksa', function ($query) {
                    $query->where('id_dokter', Auth::id());
                })
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan pasien sudah dipanggil.
            if ($antrian->status_antrian !== 'dipanggil') {
                throw ValidationException::withMessages([
                    'antrian' => 'Hanya pasien dengan status dipanggil yang dapat memulai pemeriksaan.',
                ]);
            }

            // Ubah status menjadi sedang diperiksa.
            $antrian->update([
                'status_antrian' => 'diperiksa',
            ]);

            // Kirim event untuk memperbarui tampilan secara real-time.
            event(new AntrianDiperbarui($antrian));

            return $antrian;
        });

        return back()->with([
            'message' => 'Pemeriksaan pasien berhasil dimulai.',
            'type' => 'success',
        ]);
    }
}
