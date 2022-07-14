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
    public $cuadros;
    public $fechas;
    public function __construct($datos, $cuadros, $fechas)
    {
        $this->datos   = $datos;
        $this->cuadros = $cuadros;
        $this->fechas  = $fechas;
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
                    ->with($this->datos, $this->cuadros, $this->fechas);
    }
}
