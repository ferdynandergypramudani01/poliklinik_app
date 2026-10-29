<x-layouts.app title="Pembayaran Pasien">
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">
                Pembayaran
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Lihat tagihan pemeriksaan dan unggah bukti pembayaran.
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

        <div class="space-y-5">
            @forelse ($pembayaran as $item)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">
                                Tagihan Pemeriksaan
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                No. Antrean:
                                {{ $item->periksa->daftarPoli->no_antrian ?? '-' }}
                            </p>

                            <p class="text-sm text-slate-500">
                                Dokter:
                                {{ $item->periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Total Tagihan</p>
                            <p class="text-xl font-bold text-slate-800">
                                Rp {{ number_format($item->periksa->biaya_periksa, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span
                            class="inline-block rounded-full px-3 py-1 text-sm font-medium
                            @switch($item->status)
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
                                @default
                                    bg-slate-100 text-slate-700
                            @endswitch">
                            @switch($item->status)
                                @case('menunggu_pembayaran')
                                    Menunggu Pembayaran
                                @break

                                @case('menunggu_verifikasi')
                                    Menunggu Verifikasi Admin
                                @break

                                @case('lunas')
                                    Lunas
                                @break

                                @case('ditolak')
                                    Pembayaran Ditolak
                                @break
                            @endswitch
                        </span>
                    </div>

                    @if ($item->status === 'ditolak' && $item->catatan_admin)
                        <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                            <p class="font-semibold">Alasan Penolakan</p>
                            <p class="mt-1">{{ $item->catatan_admin }}</p>
                        </div>
                    @endif

                    @if ($item->bukti_pembayaran)
                        <div class="mt-4">
                            <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}" target="_blank"
                                rel="noopener noreferrer" class="text-sm font-medium text-blue-600 hover:underline">
                                Lihat Bukti Pembayaran
                            </a>
                        </div>
                    @endif

                    @if (in_array($item->status, ['menunggu_pembayaran', 'ditolak'], true))
                        <form action="{{ route('pasien.pembayaran.upload', $item->id) }}" method="POST"
                            enctype="multipart/form-data" class="mt-5 border-t border-slate-200 pt-5">
                            @csrf

                            <label for="bukti_pembayaran_{{ $item->id }}"
                                class="mb-2 block text-sm font-medium text-slate-700">
                                {{ $item->status === 'ditolak' ? 'Unggah Ulang Bukti Pembayaran' : 'Unggah Bukti Pembayaran' }}
                            </label>

                            <input type="file" id="bukti_pembayaran_{{ $item->id }}" name="bukti_pembayaran"
                                accept="image/jpeg,image/png,image/webp" required
                                class="block w-full rounded-lg border border-slate-300 p-2 text-sm
                                       file:mr-4 file:rounded-md file:border-0
                                       file:bg-blue-50 file:px-4 file:py-2
                                       file:font-medium file:text-blue-700">

                            <p class="mt-1 text-xs text-slate-500">
                                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                            </p>

                            <button type="submit"
                                class="mt-4 rounded-lg bg-blue-600 px-5 py-2.5
                                       text-sm font-semibold text-white
                                       hover:bg-blue-700">
                                Kirim Bukti Pembayaran
                            </button>
                        </form>
                    @elseif ($item->status === 'menunggu_verifikasi')
                        <p class="mt-4 text-sm text-blue-700">
                            Bukti berhasil diunggah. Silakan tunggu verifikasi admin.
                        </p>
                    @elseif ($item->status === 'lunas')
                        <p class="mt-4 text-sm font-medium text-green-700">
                            Pembayaran telah dikonfirmasi oleh admin.
                        </p>
                    @endif
                </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-700">
                            Belum Ada Tagihan
                        </h2>
                        <p class="mt-2 text-sm text-slate-500">
                            Tagihan akan muncul setelah dokter menyelesaikan pemeriksaan Anda.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $pembayaran->links() }}
            </div>
        </div>
    </x-layouts.app>
