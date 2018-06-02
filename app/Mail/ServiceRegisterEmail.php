<?php

namespace App\Mail;

use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceRegisterEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $product;

    protected $recipients;

    /**
     * Create a new message instance.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->product = Product::find($params['product_id']);
        $this->recipients = $params['recipients'];

        foreach ($this->product->emailAttaches as $attach) {
            $this->attach(public_path($attach->url));
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('email.basic', ['text' => $this->product->email_text])
            ->subject($this->product->email_title)
            ->to($this->recipients)
            ;
    }
}
