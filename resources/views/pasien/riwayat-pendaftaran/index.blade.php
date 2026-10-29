<x-layouts.app title="Riwayat Pendaftaran">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content">
                Riwayat Pendaftaran
            </h1>
            <p class="mt-1 text-sm text-base-content/60">
                Daftar pendaftaran dan status pemeriksaan Anda.
            </p>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-0">
                @if ($riwayat->isEmpty())
                    <div class="py-12 px-6 text-center">
                        <p class="font-semibold text-base-content">
                            Belum ada riwayat pendaftaran
                        </p>
                        <p class="mt-2 text-sm text-base-content/60">
                            Pendaftaran yang Anda buat akan muncul di sini.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Poli</th>
                                    <th>Dokter</th>
                                    <th>Tanggal</th>
                                    <th>No. Antrean</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($riwayat as $item)
                                    @php
                                        $periksa = $item->periksa;
                                        $jadwal = $item->jadwalPeriksa;
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            {{ $jadwal?->dokter?->poli?->nama_poli ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $jadwal?->dokter?->nama ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($periksa?->tgl_periksa)
                                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y') }}
                                            @else
                                                {{ $item->created_at?->format('d/m/Y') ?? '-' }}
                                            @endif

                                            @if ($jadwal)
                                                <div class="text-xs text-base-content/60">
                                                    {{ $jadwal->hari }},
                                                    {{ substr($jadwal->jam_mulai, 0, 5) }}
                                                    -
                                                    {{ substr($jadwal->jam_selesai, 0, 5) }}
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge badge-outline">
                                                {{ $item->no_antrian }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($periksa)
                                                <span class="badge badge-success">
                                                    Sudah Diperiksa
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    Menunggu Pemeriksaan
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($periksa)
                                                <a href="{{ route('pasien.riwayat-pendaftaran.show', $item->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    Detail
                                                </a>
                                            @else
                                                <span class="text-xs text-base-content/50">
                                                    Belum tersedia
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
