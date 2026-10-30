<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianDiperbarui;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Pembayaran;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with([
            'pasien',
            'jadwalPeriksa',
            'periksas',
        ])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        $obats = Obat::all();

        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_daftar_poli' => ['required', 'integer', 'exists:daftar_poli,id'],
            'obat_json' => ['required', 'json'],
            'catatan' => ['nullable', 'string'],
        ]);

        $obatIds = json_decode($validated['obat_json'], true);

        if (
            ! is_array($obatIds) ||
            empty($obatIds) ||
            count($obatIds) !== count(array_unique($obatIds))
        ) {
            throw ValidationException::withMessages([
                'obat_json' => 'Pilih minimal satu obat yang valid dan tidak duplikat.',
            ]);
        }

        foreach ($obatIds as $obatId) {
            if (! is_int($obatId) && ! (is_string($obatId) && ctype_digit($obatId))) {
                throw ValidationException::withMessages([
                    'obat_json' => 'Data obat tidak valid.',
                ]);
            }
        }

        $obatIds = array_map('intval', $obatIds);

        $obats = Obat::whereIn('id', $obatIds)->get();

        if ($obats->count() !== count($obatIds)) {
            throw ValidationException::withMessages([
                'obat_json' => 'Satu atau lebih obat tidak ditemukan.',
            ]);
        }

        $totalHargaObat = $obats->sum('harga');
        $biayaPemeriksaan = 150000;
        $totalBiaya = $totalHargaObat + $biayaPemeriksaan;

        DB::transaction(function () use ($validated, $obats, $totalBiaya) {

            // Pastikan pendaftaran merupakan milik jadwal dokter yang login.
            $daftarPoli = DaftarPoli::whereKey($validated['id_daftar_poli'])
                ->whereHas('jadwalPeriksa', function ($query) {
                    $query->where('id_dokter', Auth::id());
                })
                ->lockForUpdate()
                ->firstOrFail();

            // Pemeriksaan hanya dapat disimpan ketika status sedang diperiksa.
            if ($daftarPoli->status_antrian !== 'diperiksa') {
                throw ValidationException::withMessages([
                    'id_daftar_poli' => 'Pasien belum berada dalam status pemeriksaan.',
                ]);
            }

            // Cegah pemeriksaan ganda.
            if ($daftarPoli->periksas()->exists()) {
                throw ValidationException::withMessages([
                    'id_daftar_poli' => 'Pasien ini sudah memiliki hasil pemeriksaan.',
                ]);
            }

            // Simpan hasil pemeriksaan.
            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPoli->id,
                'tgl_periksa' => now(),
                'catatan' => $validated['catatan'] ?? null,
                'biaya_periksa' => $totalBiaya,
            ]);

            // Simpan detail obat yang diberikan.
            foreach ($obats as $obat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $obat->id,
                ]);
            }

            // Buat tagihan pembayaran pasien.
            Pembayaran::create([
                'id_periksa' => $periksa->id,
                'status' => 'menunggu_pembayaran',
            ]);

            // Tandai antrian sebagai selesai.
            $daftarPoli->update([
                'status_antrian' => 'selesai',
            ]);

            // Kirim event pembaruan antrian setelah transaksi berhasil commit.
            event(new AntrianDiperbarui($daftarPoli));
        });

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Pemeriksaan berhasil disimpan dan tagihan pembayaran telah dibuat.');
    }
}
