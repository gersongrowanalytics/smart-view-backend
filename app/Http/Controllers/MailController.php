<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function getMail()
    {
        $mensaje    = "No se pudo enviar el correo";
        $usuario = "usuarioasd";
        $data = ['name' => 'Mauricio'];

        // Mail::send('testmail', $data, function($message){
        //     $message->to('gerson.vilca@tecsup.edu.pe')->subject('Verifica tu correo');
        // });
        // Mail::to('gerson.vilca@tecsup.edu.pe')->send(new TestMail($data));
        return view('testmail'); 

    }
}
