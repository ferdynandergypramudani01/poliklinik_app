<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentUploadedNotification extends Notification
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
            'type' => 'payment_uploaded',
            'title' => 'Bukti Pembayaran Baru',
            'message' => 'Pasien telah mengunggah bukti pembayaran. Silakan periksa dan lakukan verifikasi pembayaran.',
            'pembayaran_id' => $this->pembayaranId,
        ];
    }
}
