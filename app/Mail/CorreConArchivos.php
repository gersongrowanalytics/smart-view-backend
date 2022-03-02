<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreConArchivos extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje;
    public $asunto;
    public $excel;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mensaje, $asunto, $excel)
    {
        $this->mensaje = $mensaje;
        $this->asunto  = $asunto;
        $this->excel   = $excel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()

    {

        return $this->view('mails.index')
                    ->subject($this->asunto)
                    ->with($this->mensaje)
                    // ->attach('archivos_mail/01_Maestra_Precio_Aricaplast_2021.xlsx');
                    ->attach("/Sistema/ExcelCorreo/".$this->excel);



    }
}
