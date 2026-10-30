<x-layouts.app title="Dashboard Pasien">
    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard Pasien
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Pantau status pendaftaran dan antrean pemeriksaan Anda.
            </p>
        </div>

        @if ($antrianAktif)

            {{-- Informasi jadwal dokter --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <h2 class="font-semibold text-gray-800 mb-4">
                    Informasi Jadwal Pemeriksaan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <p class="text-sm text-gray-500">Dokter</p>
                        <p class="font-semibold text-gray-800">
                            {{ $antrianAktif->jadwalPeriksa->dokter->nama ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Poliklinik</p>
                        <p class="font-semibold text-gray-800">
                            {{ $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Hari</p>
                        <p class="font-semibold text-gray-800">
                            {{ $antrianAktif->jadwalPeriksa->hari }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Jam Praktik</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($antrianAktif->jadwalPeriksa->jam_mulai)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($antrianAktif->jadwalPeriksa->jam_selesai)->format('H:i') }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- Nomor antrean --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 text-center">
                    <p class="text-sm font-medium text-blue-600">
                        Nomor Antrean Anda
                    </p>

                    <p class="text-6xl font-bold text-blue-800 my-4">
                        {{ $antrianAktif->no_antrian }}
                    </p>

                    <span class="badge badge-info badge-lg">
                        Antrean Anda
                    </span>
                </div>

                <div class="bg-green-50 border border-green-100 rounded-xl p-6 text-center">
                    <p class="text-sm font-medium text-green-600">
                        Nomor Antrean Sedang Dipanggil
                    </p>

                    <p class="text-6xl font-bold text-green-800 my-4">
                        {{ $antrianSekarang->no_antrian ?? '-' }}
                    </p>

                    <span class="badge badge-success badge-lg">
                        Antrean Saat Ini
                    </span>
                </div>

            </div>

            {{-- Status antrean --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <h2 class="font-semibold text-gray-800 mb-4">
                    Status Antrean Anda
                </h2>

                @php
                    $status = $antrianAktif->status_antrian;

                    $statusLabel = [
                        'menunggu' => 'Menunggu',
                        'dipanggil' => 'Sedang Dipanggil',
                        'diperiksa' => 'Sedang Diperiksa',
                        'selesai' => 'Selesai',
                    ];
                @endphp

                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500">
                            Status saat ini
                        </p>

                        <p class="text-lg font-semibold text-gray-800 mt-1">
                            {{ $statusLabel[$status] ?? $status }}
                        </p>
                    </div>

                    @if ($status === 'menunggu')
                        <span class="badge badge-warning badge-lg">Menunggu</span>
                    @elseif ($status === 'dipanggil')
                        <span class="badge badge-info badge-lg">Dipanggil</span>
                    @elseif ($status === 'diperiksa')
                        <span class="badge badge-success badge-lg">Diperiksa</span>
                    @endif
                </div>
            </div>
        @else
            {{-- Belum ada antrean --}}
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800">
                    Anda Belum Memiliki Antrean Aktif
                </h2>

                <p class="text-sm text-gray-500 mt-2 mb-5">
                    Silakan melakukan pendaftaran pemeriksaan untuk mendapatkan nomor antrean.
                </p>

                <a href="{{ route('pasien.daftar') }}" class="btn btn-primary">
                    Daftar Pemeriksaan
                </a>
            </div>

        @endif

    </div>
</x-layouts.app>
