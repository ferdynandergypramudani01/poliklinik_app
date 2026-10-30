<?php

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('antrian.jadwal.{idJadwal}', function (User $user, $idJadwal) {

    $jadwal = JadwalPeriksa::find($idJadwal);

    if (!$jadwal) {
        return false;
    }

    // Dokter hanya boleh mengakses antrian jadwal miliknya.
    if ($user->role === 'dokter') {
        return (int) $jadwal->id_dokter === (int) $user->id;
    }

    // Pasien hanya boleh mengakses jadwal
    // yang memiliki pendaftaran atas namanya.
    if ($user->role === 'pasien') {
        return DaftarPoli::where('id_jadwal', $jadwal->id)
            ->where('id_pasien', $user->id)
            ->exists();
    }

    return false;
});
