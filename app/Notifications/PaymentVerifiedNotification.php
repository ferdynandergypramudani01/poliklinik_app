<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $pembayaranId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_verified',
            'title' => 'Pembayaran Berhasil Diverifikasi',
            'message' => 'Pembayaran pemeriksaan kamu telah diverifikasi oleh admin dan sekarang berstatus lunas.',
            'pembayaran_id' => $this->pembayaranId,
        ];
    }
}
