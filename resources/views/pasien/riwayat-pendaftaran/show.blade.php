<x-layouts.app title="Detail Pemeriksaan">
    @php
        $periksa = $daftarPoli->periksa;
        $jadwal = $daftarPoli->jadwalPeriksa;
        $detailObat = $periksa->detailPeriksas;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center
                    sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Detail Pemeriksaan
                </h1>
                <p class="mt-1 text-sm text-base-content/60">
                    Informasi hasil pemeriksaan Anda.
                </p>
            </div>

            <a href="{{ route('pasien.riwayat-pendaftaran.index') }}" class="btn btn-outline btn-sm">
                Kembali
            </a>
        </div>

        {{-- Informasi pendaftaran --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    Informasi Pendaftaran
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <p class="text-sm text-base-content/60">Poli</p>
                        <p class="font-medium">
                            {{ $jadwal?->dokter?->poli?->nama_poli ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-base-content/60">Dokter</p>
                        <p class="font-medium">
                            {{ $jadwal?->dokter?->nama ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-base-content/60">
                            Tanggal Pemeriksaan
                        </p>
                        <p class="font-medium">
                            {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-base-content/60">
                            Nomor Antrean
                        </p>
                        <p class="font-medium">
                            {{ $daftarPoli->no_antrian }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan dokter --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-lg">Catatan Dokter</h2>

                <div class="bg-base-200/50 rounded-lg p-4 mt-2">
                    {{ $periksa->catatan ?: 'Tidak ada catatan dokter.' }}
                </div>
            </div>
        </div>

        {{-- Daftar obat --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-lg">Daftar Obat</h2>

                @if ($detailObat->isEmpty())
                    <p class="text-sm text-base-content/60 mt-2">
                        Tidak ada obat yang diresepkan.
                    </p>
                @else
                    <div class="overflow-x-auto mt-2">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Obat</th>
                                    <th>Kemasan</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($detailObat as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $detail->obat?->nama_obat ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $detail->obat?->kemasan ?? '-' }}
                                        </td>
                                        <td>
                                            Rp
                                            {{ number_format($detail->obat?->harga ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Total tagihan --}}
        <div class="card bg-primary text-primary-content shadow-sm">
            <div class="card-body">
                <p class="text-sm opacity-80">Total Biaya Pemeriksaan</p>

                <p class="text-3xl font-bold">
                    Rp
                    {{ number_format($periksa->biaya_periksa ?? 0, 0, ',', '.') }}
                </p>

                <p class="text-xs opacity-80">
                    Total sesuai nilai biaya yang tersimpan pada data pemeriksaan.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
