<?php

namespace App\Events;

use App\Models\DaftarPoli;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianDiperbarui implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public int $idJadwal;
    public int $idDaftarPoli;
    public int $noAntrian;
    public string $statusAntrian;
    public ?string $waktuDipanggil;

    public function __construct(DaftarPoli $daftarPoli)
    {
        $this->idJadwal = $daftarPoli->id_jadwal;
        $this->idDaftarPoli = $daftarPoli->id;
        $this->noAntrian = $daftarPoli->no_antrian;
        $this->statusAntrian = $daftarPoli->status_antrian;
        $this->waktuDipanggil = $daftarPoli->waktu_dipanggil?->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('antrian.jadwal.' . $this->idJadwal),
        ];
    }

    public function broadcastAs(): string
    {
        return 'antrian.diperbarui';
    }

    public function broadcastWith(): array
    {
        return [
            'id_jadwal' => $this->idJadwal,
            'id_daftar_poli' => $this->idDaftarPoli,
            'no_antrian' => $this->noAntrian,
            'status_antrian' => $this->statusAntrian,
            'waktu_dipanggil' => $this->waktuDipanggil,
        ];
    }
}
