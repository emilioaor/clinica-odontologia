<?php

namespace App\Mail;

use App\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterAppointmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $appointment;

    protected $recipients;

    /**
     * Create a new message instance.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->appointment = Appointment::with(['doctor'])->find($params['appointment_id']);
        $this->recipients = $params['recipients'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('email.registerAppointment', ['appointment' => $this->appointment])
            ->subject(trans('message.email.subject.registerAppointment'))
            ->to($this->recipients)
            ;
    }
}
