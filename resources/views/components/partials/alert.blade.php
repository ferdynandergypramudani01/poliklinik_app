@if (session('message'))
    <div id="flash-alert"
        class="alert alert-{{ session('type', 'info') }}
               fixed top-5 right-5 z-50
               w-auto max-w-sm shadow-lg
               transition-opacity duration-500"
        role="alert">
        <span>{{ session('message') }}</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('flash-alert');

            if (alert) {
                setTimeout(() => {
                    alert.classList.add('opacity-0');

                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 6000);
            }
        });
    </script>
@endif
