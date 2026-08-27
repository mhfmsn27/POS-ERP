<?php

namespace Poshub\Ecommerce\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerifyNotify extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verifikasi Email - Konfirmasikan Akun Anda')
            ->line('Halo, ', $notifiable->name)
            ->line('Kami ingin memastikan keamanan akun Anda dengan melakukan verifikasi email. Mohon Masukkan Kode Di Bawah ini')
            ->line('Kode Verifikasi Email : '.$notifiable->code_verify_email) 
            ->line('Kode ini akan kadaluarsa hingga 10 menit kedepan, jadi pastikan kamu melakukan konfirmasi');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
