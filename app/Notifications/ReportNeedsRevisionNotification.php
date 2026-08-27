<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportNeedsRevisionNotification extends Notification
{
    use Queueable;

    protected $report;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct($report, $notes)
    {
        $this->report = $report;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reportNumber = $this->report->report_number ?? '-';
        $voterName = $this->report->voter_name ?? '-';

        return (new MailMessage)
            ->subject('SIPKP - Laporan Memerlukan Perbaikan')
            ->greeting('Halo ' . $notifiable->full_name . ',')
            ->line('Laporan Anda dengan nomor tiket ' . $reportNumber . ' (atas nama pemilih: ' . $voterName . ') memerlukan perbaikan.')
            ->line('Catatan dari Sub Operator:')
            ->line('"' . ($this->notes ?? 'Tidak ada catatan khusus.') . '"')
            ->action('Masuk ke SIPKP', url('/pelapor/dashboard'))
            ->line('Email ini dikirim secara otomatis. Mohon tidak membalas email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
