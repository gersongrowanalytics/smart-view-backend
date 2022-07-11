<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailInformarStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $datos;
    public function __construct($datos)
    {
        $this->datos = $datos;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('smartview@grow-analytics.com', 'Grow: TeamSupport')
                    ->view('CorreoInformarStatus')
                    ->subject('STATUS')
                    ->with($this->datos);
    }
}
