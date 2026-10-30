<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-6 gap-4 sticky top-0 z-30 shadow-sm">

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" class="btn btn-square btn-ghost lg:hidden">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-base-content/50">Poliklinik</span>
            <i data-lucide="chevron-right" class="w-4 h-4 text-base-content/30"></i>
            <span class="font-semibold text-base-content">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Notification Dropdown --}}
    @auth
        @php
            $userNotifications = auth()->user()->notifications()->latest()->take(5)->get();

            $unreadCount = auth()->user()->unreadNotifications()->count();
        @endphp

        <div class="dropdown dropdown-end">

            {{-- Bell Button --}}
            <label tabindex="0" class="btn btn-square btn-ghost relative" aria-label="Notifikasi">

                <i data-lucide="bell" class="w-5 h-5"></i>

                {{-- Unread Badge --}}
                @if ($unreadCount > 0)
                    <span class="absolute top-1 right-1 badge badge-error badge-xs min-w-4 h-4 px-1 text-white font-bold">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif

            </label>

            {{-- Dropdown Content --}}
            <div tabindex="0"
                class="dropdown-content z-[50] mt-3 w-80 sm:w-96 bg-base-100 rounded-xl shadow-xl border border-base-300 overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-base-300">

                    <div>
                        <h3 class="font-semibold text-base-content">
                            Notifikasi
                        </h3>

                        <p class="text-xs text-base-content/50 mt-0.5">
                            {{ $unreadCount }} belum dibaca
                        </p>
                    </div>

                    <i data-lucide="bell-ring" class="w-5 h-5 text-base-content/50"></i>

                </div>

                {{-- Notification List --}}
                <div class="max-h-96 overflow-y-auto">

                    @forelse ($userNotifications as $notification)
                        <div
                            class="flex items-start gap-3 p-4 border-b border-base-200
                            hover:bg-base-200/50 transition
                            {{ $notification->read_at ? '' : 'bg-primary/5' }}">

                            {{-- Notification Icon --}}
                            <div
                                class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center
                                {{ ($notification->data['type'] ?? '') === 'payment_uploaded'
                                    ? 'bg-info/10 text-info'
                                    : 'bg-success/10 text-success' }}">

                                @if (($notification->data['type'] ?? '') === 'payment_uploaded')
                                    <i data-lucide="file-up" class="w-5 h-5"></i>
                                @else
                                    <i data-lucide="circle-check" class="w-5 h-5"></i>
                                @endif

                            </div>

                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">

                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-semibold text-base-content">
                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                    </h4>

                                    @if (!$notification->read_at)
                                        <span class="w-2 h-2 rounded-full bg-primary shrink-0"></span>
                                    @endif
                                </div>

                                <p class="text-xs text-base-content/70 mt-1 line-clamp-2">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                <p class="text-[11px] text-base-content/40 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>

                            </div>

                            {{-- Mark As Read --}}
                            @if (!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="text-xs text-primary hover:underline whitespace-nowrap">
                                        Baca
                                    </button>

                                </form>
                            @endif

                        </div>

                    @empty

                        {{-- Empty State --}}
                        <div class="flex flex-col items-center justify-center py-10 px-4 text-center">

                            <i data-lucide="bell-off" class="w-10 h-10 text-base-content/30"></i>

                            <p class="text-sm font-medium text-base-content/70 mt-3">
                                Belum Ada Notifikasi
                            </p>

                            <p class="text-xs text-base-content/40 mt-1">
                                Saat ini belum ada pemberitahuan untuk Anda.
                            </p>

                        </div>
                    @endforelse

                </div>

                {{-- Footer --}}
                <a href="{{ route('notifications.index') }}"
                    class="flex items-center justify-center gap-2 p-3 text-sm font-semibold text-primary hover:bg-base-200 transition">

                    Lihat Semua Notifikasi

                    <i data-lucide="arrow-right" class="w-4 h-4"></i>

                </a>

            </div>

        </div>
    @endauth

    {{-- Fullscreen --}}
    <button onclick="toggleFullscreen()" class="btn btn-square btn-ghost">
        <i id="fsIcon" class="fas fa-expand w-5 h-5"></i>
    </button>

    {{-- User Info --}}
    <div class="flex items-center gap-3">

        <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold leading-tight">
                {{ auth()->user()->nama ?? (auth()->user()->name ?? 'Pengguna') }}
            </div>

            <div class="text-xs text-base-content/50 leading-tight">
                {{ auth()->user()->role ?? 'Admin Sistem' }}
            </div>
        </div>

        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->nama ?? (auth()->user()->name ?? 'U'), 0, 1)) }}
                </span>
            </div>
        </div>

    </div>

</header>

<script>
    function toggleFullscreen() {
        const icon = document.getElementById('fsIcon');

        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            document.exitFullscreen();
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }

    function renderLucideIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderLucideIcons);
    } else {
        renderLucideIcons();
    }
</script>
