<?php

namespace App\Notifications;

use App\PatientHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendLabNotification extends Notification
{
    use Queueable;

    /**
     * Historial de servicio registrado
     *
     * @var PatientHistory
     */
    protected $patientHistory;

    /**
     * Horas antes que aplica la notificacion
     *
     * @var
     */
    protected $hours;

    /**
     * Create a new notification instance.
     *
     * @param PatientHistory $patientHistory
     * @param int $hours
     */
    public function __construct(PatientHistory $patientHistory, $hours)
    {
        $this->patientHistory = $patientHistory;
        $this->hours = $hours;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'delivery_date' => $this->patientHistory->delivery_date,
            'doctor' => $this->patientHistory->doctor,
            'assistant' => $this->patientHistory->assistant,
            'responsible' => $this->patientHistory->responsible,
            'supplier' => $this->patientHistory->supplier,
            'patient' => $this->patientHistory->patient,
            'hours' => $this->hours
        ];
    }
}
