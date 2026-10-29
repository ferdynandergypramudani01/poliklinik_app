<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    /**
     * Menampilkan tagihan milik pasien yang sedang login.
     */
    public function index()
    {
        $pembayaran = Pembayaran::whereHas(
            'periksa.daftarPoli',
            function ($query) {
                $query->where('id_pasien', Auth::id());
            }
        )
            ->with([
                'periksa.daftarPoli.jadwalPeriksa.dokter',
            ])
            ->latest()
            ->paginate(10);

        return view('pasien.pembayaran.index', compact('pembayaran'));
    }

    /**
     * Mengunggah bukti pembayaran.
     */
    public function upload(Request $request, $id)
    {
        $validated = $request->validate([
            'bukti_pembayaran' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'bukti_pembayaran.required' => 'Silakan pilih foto bukti pembayaran.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format yang diperbolehkan: JPG, JPEG, PNG, atau WEBP.',
            'bukti_pembayaran.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $pembayaran = Pembayaran::whereHas(
            'periksa.daftarPoli',
            function ($query) {
                $query->where('id_pasien', Auth::id());
            }
        )->findOrFail($id);

        $pathLama = null;

        $pathBaru = $validated['bukti_pembayaran']
            ->store('pembayaran', 'public');

        try {
            DB::transaction(function () use (
                $id,
                $pathBaru,
                &$pathLama
            ) {
                $pembayaran = Pembayaran::whereKey($id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Pastikan kepemilikan tetap valid.
                $milikPasien = $pembayaran->periksa()
                    ->whereHas('daftarPoli', function ($query) {
                        $query->where('id_pasien', Auth::id());
                    })
                    ->exists();

                abort_unless($milikPasien, 404);

                if (!in_array($pembayaran->status, [
                    'menunggu_pembayaran',
                    'ditolak',
                ], true)) {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' =>
                            'Tagihan ini tidak dapat menerima bukti pembayaran saat ini.',
                    ]);
                }

                $pathLama = $pembayaran->bukti_pembayaran;

                $pembayaran->update([
                    'bukti_pembayaran' => $pathBaru,
                    'status' => 'menunggu_verifikasi',
                    'catatan_admin' => null,
                    'verified_by' => null,
                    'verified_at' => null,
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($pathBaru);

            throw $e;
        }

        if ($pathLama) {
            Storage::disk('public')->delete($pathLama);
        }

        return redirect()
            ->route('pasien.pembayaran.index')
            ->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu verifikasi admin.');
    }
}
