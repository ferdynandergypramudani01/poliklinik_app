<x-layouts.app title="Detail Verifikasi Pembayaran">
    <div class="p-6">
        <div class="mb-6">
            <a href="{{ route('admin.pembayaran.index') }}" class="text-sm font-medium text-blue-600 hover:underline">
                &larr; Kembali ke Verifikasi Pembayaran
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-800">
                Detail Verifikasi Pembayaran
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Periksa informasi tagihan dan bukti pembayaran pasien.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-100 p-4 text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Informasi Tagihan --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-semibold text-slate-800">
                    Informasi Tagihan
                </h2>

                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-slate-500">Nama Pasien</p>
                        <p class="font-medium text-slate-800">
                            {{ $pembayaran->periksa->daftarPoli->pasien->nama ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Nomor Antrean</p>
                        <p class="font-medium text-slate-800">
                            {{ $pembayaran->periksa->daftarPoli->no_antrian ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Dokter</p>
                        <p class="font-medium text-slate-800">
                            {{ $pembayaran->periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Tanggal Pemeriksaan</p>
                        <p class="font-medium text-slate-800">
                            {{ $pembayaran->periksa->tgl_periksa
                                ? \Carbon\Carbon::parse($pembayaran->periksa->tgl_periksa)->format('d-m-Y H:i')
                                : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Biaya Pemeriksaan</p>
                        <p class="font-medium text-slate-800">
                            Rp {{ number_format($pembayaran->periksa->biaya_periksa, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-slate-500">Total Tagihan</p>
                        <p class="text-2xl font-bold text-blue-700">
                            Rp {{ number_format($pembayaran->periksa->biaya_periksa, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Status Pembayaran</p>

                        <span
                            class="mt-1 inline-block rounded-full px-3 py-1 text-sm font-medium
                            @switch($pembayaran->status)
                                @case('menunggu_pembayaran')
                                    bg-yellow-100 text-yellow-700
                                    @break
                                @case('menunggu_verifikasi')
                                    bg-blue-100 text-blue-700
                                    @break
                                @case('lunas')
                                    bg-green-100 text-green-700
                                    @break
                                @case('ditolak')
                                    bg-red-100 text-red-700
                                    @break
                            @endswitch">
                            {{ str_replace('_', ' ', ucfirst($pembayaran->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Bukti Pembayaran --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-semibold text-slate-800">
                    Bukti Pembayaran
                </h2>

                @if ($pembayaran->bukti_pembayaran)
                    <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank"
                        rel="noopener noreferrer" class="block">
                        <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                            alt="Bukti pembayaran pasien"
                            class="max-h-[500px] w-full rounded-lg border border-slate-200 object-contain">
                    </a>

                    <p class="mt-3 text-xs text-slate-500">
                        Klik gambar untuk melihat ukuran penuh.
                    </p>
                @else
                    <div class="rounded-lg bg-slate-50 p-8 text-center text-slate-500">
                        Pasien belum mengunggah bukti pembayaran.
                    </div>
                @endif

                @if ($pembayaran->catatan_admin)
                    <div class="mt-5 rounded-lg bg-red-50 p-4">
                        <p class="font-semibold text-red-700">
                            Catatan Admin
                        </p>
                        <p class="mt-1 text-sm text-red-600">
                            {{ $pembayaran->catatan_admin }}
                        </p>
                    </div>
                @endif
            </div>
        </div>



        {{-- Aksi Verifikasi --}}
        @if ($pembayaran->status === 'menunggu_verifikasi')
            <div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-800">
                    Tindakan Verifikasi
                </h2>

                <p class="mb-5 text-sm text-slate-600">
                    Pastikan bukti pembayaran valid sebelum mengonfirmasi pembayaran.
                </p>

                <div class="flex flex-col gap-6 md:flex-row">
                    {{-- Konfirmasi Pembayaran --}}
                    <form action="{{ route('admin.pembayaran.konfirmasi', $pembayaran->id) }}" method="POST"
                        class="flex-1"
                        onsubmit="return confirm('Apakah Anda yakin ingin mengonfirmasi pembayaran ini?')">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-lg
                           bg-green-600 px-5 py-3 font-semibold text-white
                           shadow-sm transition hover:bg-green-700">
                            <i class="fas fa-circle-check"></i>
                            Konfirmasi Pembayaran
                        </button>
                    </form>

                    {{-- Tolak Pembayaran --}}
                    <form action="{{ route('admin.pembayaran.tolak', $pembayaran->id) }}" method="POST"
                        class="flex-1">
                        @csrf
                        @method('PATCH')

                        <label for="catatan_admin" class="mb-2 block text-sm font-medium text-slate-700">
                            Alasan Penolakan
                        </label>

                        <textarea id="catatan_admin" name="catatan_admin" rows="3" maxlength="1000" required
                            class="w-full rounded-lg border border-slate-300 p-3 text-sm
                           focus:border-red-500 focus:outline-none focus:ring-1
                           focus:ring-red-500"
                            placeholder="Contoh: Bukti pembayaran tidak jelas atau nominal tidak sesuai.">{{ old('catatan_admin') }}</textarea>

                        @error('catatan_admin')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <button type="submit"
                            class="mt-3 flex w-full items-center justify-center gap-2
                           rounded-lg bg-red-600 px-5 py-3 font-semibold
                           text-white shadow-sm transition hover:bg-red-700">
                            <i class="fas fa-circle-xmark"></i>
                            Tolak Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        @elseif ($pembayaran->status === 'lunas')
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-5 text-green-700">
                <p class="font-semibold">
                    <i class="fas fa-circle-check mr-2"></i>
                    Pembayaran telah dikonfirmasi.
                </p>

                <p class="mt-2 text-sm">
                    @if ($pembayaran->verified_at)
                        Diverifikasi pada
                        {{ $pembayaran->verified_at->format('d-m-Y H:i') }}.
                    @endif

                    @if ($pembayaran->verifier)
                        Oleh
                        {{ $pembayaran->verifier->nama ?? ($pembayaran->verifier->name ?? 'Admin') }}.
                    @endif
                </p>
            </div>
        @elseif ($pembayaran->status === 'ditolak')
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-5 text-red-700">
                <p class="font-semibold">
                    <i class="fas fa-circle-xmark mr-2"></i>
                    Bukti pembayaran ditolak.
                </p>

                <p class="mt-2 text-sm">
                    Pasien dapat mengunggah ulang bukti pembayaran melalui menu Pembayaran.
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>
