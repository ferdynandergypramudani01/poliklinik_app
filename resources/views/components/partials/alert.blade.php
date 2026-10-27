@php
    $action = session('action', 'create');

    $notification = match ($action) {
        'create' => [
            'color' => 'bg-green-100 border-green-500 text-green-800',
            'iconColor' => 'text-green-600',
            'icon' => 'check',
        ],
        'edit' => [
            'color' => 'bg-yellow-100 border-yellow-500 text-yellow-800',
            'iconColor' => 'text-yellow-600',
            'icon' => 'edit',
        ],
        'destroy' => [
            'color' => 'bg-red-100 border-red-500 text-red-800',
            'iconColor' => 'text-red-600',
            'icon' => 'trash',
        ],
        default => [
            'color' => 'bg-blue-100 border-blue-500 text-blue-800',
            'iconColor' => 'text-blue-600',
            'icon' => 'check',
        ],
    };
@endphp

@if (session('message'))
    <div id="flash-alert"
        class="fixed top-5 right-6 z-50
               flex items-center gap-3
               w-auto max-w-sm
               border-l-4 rounded-lg
               px-4 py-3 shadow-lg
               {{ $notification['color'] }}
               translate-x-full opacity-0
               transition-all duration-500 ease-in-out"
        role="alert">
        {{-- Ikon notifikasi --}}
        <div class="{{ $notification['iconColor'] }} shrink-0">
            @if ($notification['icon'] === 'check')
                {{-- Checklist --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            @elseif ($notification['icon'] === 'edit')
                {{-- Pena --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
            @elseif ($notification['icon'] === 'trash')
                {{-- Tempat sampah --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-12 0h18" />
                </svg>
            @endif
        </div>

        {{-- Pesan notifikasi --}}
        <span class="text-sm font-medium">
            {{ session('message') }}
        </span>

        {{-- Tombol tutup --}}
        <button type="button" onclick="document.getElementById('flash-alert')?.remove()"
            class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('flash-alert');

            if (!alert) return;

            // Animasi masuk dari kanan
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    alert.classList.remove(
                        'translate-x-full',
                        'opacity-0'
                    );

                    alert.classList.add(
                        'translate-x-0',
                        'opacity-100'
                    );
                });
            });

            // Hilangkan setelah 6 detik
            setTimeout(() => {
                if (!alert.isConnected) return;

                alert.classList.remove(
                    'translate-x-0',
                    'opacity-100'
                );

                alert.classList.add(
                    'translate-x-full',
                    'opacity-0'
                );

                setTimeout(() => alert.remove(), 500);
            }, 6000);
        });
    </script>
@endif
