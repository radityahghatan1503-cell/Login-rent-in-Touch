<?php

namespace App\Notifications;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingReceived extends Notification
{
    use Queueable;

    public function __construct(private Pemesanan $pemesanan)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'booking_received',
            'title' => 'Pengajuan booking baru',
            'message' => 'Pesanan ' . $this->pemesanan->kode_pemesanan . ' menunggu konfirmasi.',
            'booking_id' => $this->pemesanan->id_pemesanan,
            'booking_code' => $this->pemesanan->kode_pemesanan,
            'tenant_id' => $this->pemesanan->id_tenant,
            'url' => route('mitra.pemesanan.show', $this->pemesanan->id_pemesanan),
        ];
    }
}
