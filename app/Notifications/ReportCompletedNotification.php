<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportCompletedNotification extends Notification
{
    use Queueable;

    protected $report;

    /**
     * Create a new notification instance.
     */
    public function __construct($report)
    {
        $this->report = $report;
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

        return (new MailMessage)
            ->subject('SIPKP - Laporan Telah Selesai Diproses')
            ->greeting('Halo ' . $notifiable->full_name . ',')
            ->line('Laporan Anda dengan nomor tiket ' . $reportNumber . ' telah selesai diproses.')
            ->action('Lihat Detail', url('/pelapor/dashboard'))
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
