<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailInformarStatusV2 extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    // public $datos;
    // public $cuadros;
    public function __construct()
    {
        // $this->datos   = $datos;
        // $this->cuadros = $cuadros;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('smartview@grow-analytics.com', 'Grow: TeamSupport')
                    ->view('CorreoInformarStatusV2')
                    ->subject('STATUS V2');
                    // ->with($this->datos, $this->cuadros);
    }
}
