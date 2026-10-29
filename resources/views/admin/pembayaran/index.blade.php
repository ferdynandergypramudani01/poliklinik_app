<x-layouts.app title="Verifikasi Pembayaran">
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">
                Verifikasi Pembayaran
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Periksa bukti pembayaran pasien sebelum mengonfirmasi tagihan.
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

        <div class="overflow-x-auto rounded-xl bg-white shadow">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="px-5 py-4">No.</th>
                        <th class="px-5 py-4">Pasien</th>
                        <th class="px-5 py-4">No. Antrean</th>
                        <th class="px-5 py-4">Biaya</th>
                        <th class="px-5 py-4">Bukti Pembayaran</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse ($pembayaran as $item)
                        <tr>
                            <td class="px-5 py-4">
                                {{ $pembayaran->firstItem() + $loop->index }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $item->periksa->daftarPoli->pasien->nama ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $item->periksa->daftarPoli->no_antrian ?? '-' }}
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                Rp {{ number_format($item->periksa->biaya_periksa, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                @if ($item->bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}" target="_blank"
                                        rel="noopener noreferrer" class="font-medium text-blue-600 hover:underline">
                                        Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-red-500">Belum ada bukti</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <a href="{{ route('admin.pembayaran.show', $item->id) }}"
                                    class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                    Periksa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                Belum ada pembayaran yang menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $pembayaran->links() }}
        </div>
    </div>
</x-layouts.app>
