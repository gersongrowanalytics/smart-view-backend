<?php

namespace App\Mail\Promociones;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailPromocionesNuevas extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $asunto;
    public function __construct($data, $asunto)
    {
        $this->data = $data;
        $this->asunto = $asunto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('SmartView@grow-analytics.com', 'Grow: TeamSupport')
                    ->view('Promociones.PromocionesNuevas')
                    // ->subject('Promociones Nuevas')
                    ->subject($this->asunto)
                    ->with($this->data);
    }
}
