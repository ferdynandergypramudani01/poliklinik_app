<x-layouts.app title="Notifikasi">

    <div class="p-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Notifikasi
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Informasi dan pemberitahuan aktivitas akun Anda.
                </p>
            </div>

            @if (auth()->user()->unreadNotifications()->exists())
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Daftar Notifikasi --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            @forelse ($notifications as $notification)
                <div
                    class="flex items-start gap-4 p-5 border-b border-slate-100
                    {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/60' }}">

                    {{-- Icon --}}
                    <div
                        class="w-11 h-11 shrink-0 rounded-full flex items-center justify-center
                        {{ $notification->data['type'] === 'payment_uploaded'
                            ? 'bg-blue-100 text-blue-600'
                            : 'bg-green-100 text-green-600' }}">

                        @if ($notification->data['type'] === 'payment_uploaded')
                            <i class="fa-solid fa-file-arrow-up"></i>
                        @else
                            <i class="fa-solid fa-circle-check"></i>
                        @endif
                    </div>

                    {{-- Konten --}}
                    <div class="flex-1 min-w-0">

                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-slate-800">
                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                            </h3>

                            @if (!$notification->read_at)
                                <span class="px-2 py-0.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                    Baru
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-slate-600 mt-1">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <p class="text-xs text-slate-400 mt-2">
                            <i class="fa-regular fa-clock mr-1"></i>
                            {{ $notification->created_at->diffForHumans() }}
                        </p>

                    </div>

                    {{-- Tombol Tandai Dibaca --}}
                    @if (!$notification->read_at)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="text-xs font-medium text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                Tandai Dibaca
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-slate-400 whitespace-nowrap">
                            Sudah dibaca
                        </span>
                    @endif

                </div>

            @empty

                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">

                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <i class="fa-regular fa-bell-slash text-2xl text-slate-400"></i>
                    </div>

                    <h3 class="font-semibold text-slate-700">
                        Belum Ada Notifikasi
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Saat ini belum ada pemberitahuan untuk Anda.
                    </p>

                </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $notifications->links() }}
        </div>

    </div>

</x-layouts.app>
