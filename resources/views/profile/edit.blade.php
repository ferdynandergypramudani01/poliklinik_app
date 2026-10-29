<x-layouts.app title="Profile">
    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-base-content">
                Profile
            </h1>

            <p class="mt-1 text-sm text-base-content/70">
                Kelola akun dan keamanan password Anda.
            </p>
        </div>

        {{-- Notifikasi sukses --}}
        @if (session('message'))
            <div role="alert" class="alert alert-success shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>

                <span>{{ session('message') }}</span>
            </div>
        @endif

        {{-- Form ganti password --}}
        <div class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body gap-5">

                <div>
                    <h2 class="text-lg font-semibold">
                        Ganti Password
                    </h2>

                    <p class="mt-1 text-sm text-base-content/70">
                        Gunakan password minimal 8 karakter.
                    </p>
                </div>

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Password lama --}}
                    <div class="form-control w-full">
                        <label for="current_password" class="label">
                            <span class="label-text font-medium">
                                Password Lama
                            </span>
                        </label>

                        <input type="password" id="current_password" name="current_password"
                            class="input input-bordered w-full
                                @error('current_password') input-error @enderror"
                            placeholder="Masukkan password lama" autocomplete="current-password" required />

                        @error('current_password')
                            <p class="mt-2 text-sm text-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password baru --}}
                    <div class="form-control w-full">
                        <label for="password" class="label">
                            <span class="label-text font-medium">
                                Password Baru
                            </span>
                        </label>

                        <input type="password" id="password" name="password"
                            class="input input-bordered w-full
                                @error('password') input-error @enderror"
                            placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required />

                        @error('password')
                            <p class="mt-2 text-sm text-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Konfirmasi password baru --}}
                    <div class="form-control w-full">
                        <label for="password_confirmation" class="label">
                            <span class="label-text font-medium">
                                Konfirmasi Password Baru
                            </span>
                        </label>

                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="input input-bordered w-full
                                @error('password_confirmation')
                                    input-error
                                @enderror"
                            placeholder="Masukkan ulang password baru" autocomplete="new-password" minlength="8"
                            required />

                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tombol submit --}}
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn btn-primary">
                            Simpan Password
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
