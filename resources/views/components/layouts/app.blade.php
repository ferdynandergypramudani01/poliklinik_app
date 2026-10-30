<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Poliklinik' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap"
        rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Vite --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body>

    <div class="app-wrapper">

        {{-- SIDEBAR --}}
        <div id="appSidebar" class="sidebar-fixed">
            @include('components.partials.sidebar')
        </div>

        {{-- OVERLAY --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- MAIN --}}
        <div class="main-content">

            @include('components.partials.header')

            <div class="main-scroll">

                @if (session('success'))
                    <div class="alert alert-success mb-4 rounded-xl shadow-sm">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error mb-4 rounded-xl shadow-sm">
                        <i class="fas fa-circle-xmark"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}

            </div>

            @include('components.partials.footer')

        </div>

    </div>

    {{-- Application Scripts --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('open');

            overlay.style.display =
                sidebar.classList.contains('open') ? 'block' : 'none';
        }

        function toggleFullscreen() {
            const icon = document.getElementById('fsIcon');

            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();

                if (icon) {
                    icon.className = 'fas fa-compress w-5 h-5';
                }
            } else {
                document.exitFullscreen();

                if (icon) {
                    icon.className = 'fas fa-expand w-5 h-5';
                }
            }
        }

        // Render Lucide Icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            } else {
                console.error('Lucide Icons gagal dimuat.');
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
