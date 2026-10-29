<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VerifikasiPembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran yang menunggu verifikasi.
     */
    public function index()
    {
        $pembayaran = Pembayaran::with([
            'periksa.daftarPoli.pasien',
            'periksa.daftarPoli.jadwalPeriksa.dokter',
        ])
            ->where('status', 'menunggu_verifikasi')
            ->latest()
            ->paginate(10);

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    /**
     * Menampilkan detail tagihan dan bukti pembayaran.
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'periksa.daftarPoli.pasien',
            'periksa.daftarPoli.jadwalPeriksa.dokter',
            'periksa.detailPeriksas.obat',
            'verifier',
        ])->findOrFail($id);

        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Mengonfirmasi pembayaran menjadi lunas.
     */
    public function konfirmasi($id)
    {
        DB::transaction(function () use ($id) {
            $pembayaran = Pembayaran::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pembayaran->status !== 'menunggu_verifikasi') {
                throw ValidationException::withMessages([
                    'pembayaran' =>
                        'Pembayaran ini tidak sedang menunggu verifikasi.',
                ]);
            }

            $pembayaran->update([
                'status' => 'lunas',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'catatan_admin' => null,
            ]);
        });

        return redirect()
            ->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi dan berstatus lunas.');
    }

    /**
     * Menolak bukti pembayaran.
     */
    public function tolak(Request $request, $id)
    {
        $validated = $request->validate([
            'catatan_admin' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'catatan_admin.required' =>
                'Alasan penolakan wajib diisi.',
            'catatan_admin.max' =>
                'Alasan penolakan maksimal 1000 karakter.',
        ]);

        DB::transaction(function () use ($id, $validated) {
            $pembayaran = Pembayaran::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pembayaran->status !== 'menunggu_verifikasi') {
                throw ValidationException::withMessages([
                    'pembayaran' =>
                        'Pembayaran ini tidak sedang menunggu verifikasi.',
                ]);
            }

            $pembayaran->update([
                'status' => 'ditolak',
                'catatan_admin' => $validated['catatan_admin'],
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.pembayaran.index')
            ->with('success', 'Bukti pembayaran ditolak. Pasien dapat mengunggah ulang bukti.');
    }
}
