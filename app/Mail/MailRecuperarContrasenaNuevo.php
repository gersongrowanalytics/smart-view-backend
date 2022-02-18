<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailRecuperarContrasenaNuevo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('SmartView@grow-analytics.com.pe', 'SmartView')
                    ->view('CorreoRecupearContrasenaNuevo')
                    ->subject('RECUPERAR CONTRASEÑA SMARTVIEW')
                    ->with($this->data);
    }
}
