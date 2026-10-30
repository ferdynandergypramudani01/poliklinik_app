 <x-layouts.app title="Dashboard Dokter">
     <div class="space-y-6">

         {{-- Header --}}
         <div>
             <h1 class="text-2xl font-bold text-gray-800">
                 Dashboard Dokter
             </h1>
             <p class="text-sm text-gray-500 mt-1">
                 Informasi jadwal praktik dan antrean pasien Anda.
             </p>
         </div>

         @forelse ($jadwals as $jadwal)
             @php
                 $menunggu = $jadwal->daftarPolis->where('status_antrian', 'menunggu');

                 $sedangDilayani = $jadwal->daftarPolis->whereIn('status_antrian', ['dipanggil', 'diperiksa']);

                 $pasienAktif = $sedangDilayani->first();
             @endphp

             <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-5">

                 {{-- Jadwal --}}
                 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                     <div>
                         <h2 class="text-lg font-semibold text-gray-800">
                             {{ $jadwal->hari }}
                         </h2>
                         <p class="text-sm text-gray-500">
                             {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                             -
                             {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                         </p>
                     </div>

                     <span class="badge badge-primary badge-outline">
                         Jadwal Praktik
                     </span>
                 </div>

                 {{-- Statistik --}}
                 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                     <div class="bg-blue-50 rounded-lg p-4">
                         <p class="text-sm text-blue-600">
                             Total Menunggu
                         </p>
                         <p class="text-2xl font-bold text-blue-800">
                             {{ $menunggu->count() }}
                         </p>
                     </div>

                     <div class="bg-green-50 rounded-lg p-4">
                         <p class="text-sm text-green-600">
                             Sedang Dipanggil
                         </p>
                         <p class="text-2xl font-bold text-green-800">
                             {{ $sedangDilayani->count() }}
                         </p>
                     </div>

                     <div class="bg-gray-50 rounded-lg p-4">
                         <p class="text-sm text-gray-600">
                             Total Antrean Aktif
                         </p>
                         <p class="text-2xl font-bold text-gray-800">
                             {{ $jadwal->daftarPolis->count() }}
                         </p>
                     </div>

                 </div>

                 {{-- Antrean saat ini --}}
                 <div class="border rounded-lg p-4">
                     <h3 class="font-semibold text-gray-700 mb-3">
                         Antrean Sedang Diproses
                     </h3>

                     @if ($pasienAktif)
                         <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                             <div>
                                 <p class="text-sm text-gray-500">
                                     Nomor Antrean
                                 </p>
                                 <p class="text-3xl font-bold text-green-600">
                                     {{ $pasienAktif->no_antrian }}
                                 </p>
                                 <p class="text-sm text-gray-700 mt-1">
                                     {{ $pasienAktif->pasien->name ?? 'Nama pasien tidak tersedia' }}
                                 </p>
                             </div>

                             <div class="flex flex-col sm:items-end gap-3">
                                 <span
                                     class="badge {{ $pasienAktif->status_antrian === 'dipanggil' ? 'badge-warning' : 'badge-success' }}">
                                     {{ $pasienAktif->status_antrian === 'dipanggil' ? 'Dipanggil' : 'Sedang Diperiksa' }}
                                 </span>

                                 {{-- Mulai Pemeriksaan --}}
                                 @if ($pasienAktif->status_antrian === 'dipanggil')
                                     <form action="{{ route('dokter.antrian.mulai', ['id' => $pasienAktif->id]) }}"
                                         method="POST">
                                         @csrf

                                         <button type="submit" class="btn btn-success">
                                             Mulai Pemeriksaan
                                         </button>
                                     </form>
                                 @endif
                             </div>
                         </div>
                     @else
                         <p class="text-sm text-gray-500">
                             Belum ada pasien yang dipanggil.
                         </p>
                     @endif
                 </div>

                 {{-- Daftar pasien menunggu --}}
                 <div>
                     <h3 class="font-semibold text-gray-700 mb-3">
                         Daftar Pasien Menunggu
                     </h3>

                     @if ($menunggu->isNotEmpty())
                         <div class="overflow-x-auto">
                             <table class="table table-zebra">
                                 <thead>
                                     <tr>
                                         <th>Nomor</th>
                                         <th>Nama Pasien</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @foreach ($menunggu as $pasien)
                                         <tr>
                                             <td class="font-semibold">
                                                 {{ $pasien->no_antrian }}
                                             </td>
                                             <td>
                                                 {{ $pasien->pasien->name ?? '-' }}
                                             </td>
                                             <td>
                                                 <span class="badge badge-warning">
                                                     Menunggu
                                                 </span>
                                             </td>
                                         </tr>
                                     @endforeach
                                 </tbody>
                             </table>
                         </div>
                     @else
                         <p class="text-sm text-gray-500">
                             Tidak ada pasien yang sedang menunggu.
                         </p>
                     @endif
                 </div>

                 {{-- Tombol panggil --}}
                 <div class="flex justify-end">
                     <form action="{{ route('dokter.antrian.panggil', ['idJadwal' => $jadwal->id]) }}" method="POST">
                         @csrf

                         <button type="submit" class="btn btn-primary" @disabled($menunggu->isEmpty() || $pasienAktif)>
                             Panggil Antrean Berikutnya
                         </button>
                     </form>
                 </div>

             </div>
         @empty
             <div class="bg-white border rounded-xl p-8 text-center">
                 <h2 class="font-semibold text-gray-700">
                     Belum Ada Jadwal Praktik
                 </h2>
                 <p class="text-sm text-gray-500 mt-2">
                     Silakan tambahkan jadwal praktik melalui menu Jadwal Periksa.
                 </p>
             </div>
         @endforelse

     </div>
 </x-layouts.app>
