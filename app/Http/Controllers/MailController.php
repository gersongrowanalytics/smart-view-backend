<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestMail;
use App\Mail\MailRecuperarContrasena;
use App\Mail\MailRecuperarContrasenaOutlook;
use App\Mail\MailCrearUsuarioOutlook;
use Illuminate\Support\Facades\Mail;
use App\usuusuarios;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MailController extends Controller
{
    public function getMail()
    {
        
        $data = ['nombre' => 'Gerson Vilca Alvarez', "usuario" => "Gerson", "contrasena" => "1234", "correo" => "gerson@hotmail.com"];


        Mail::to([
            'gerson.vilca@grow-analytics.com.pe',
            'Jose.Cruz@grow-analytics.com.pe',
            'Frank.Martinez@grow-analytics.com.pe'
        ])->send(new TestMail($data));
        return view('testmail')->with($data);
    }

    public function recuperarContrasena(Request $request)
    {

        $correo = $request['correoElectronico'];

        $usu = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                            ->where('usuusuarios.usucorreo', $correo)
                            ->first([
                                'usuusuarios.usuid', 
                                'usuusuarios.usuusuario',
                                'usuusuarios.usucontrasena',
                                'per.pernombrecompleto'
                            ]);

        $respuesta  = true;
        $mensaje    = "Se envio el correo de recuperación satisfactoriamente";

        if($usu){

            $nuevaContrasena = Str::random(6);

            $usu->usucontrasena = Hash::make($nuevaContrasena);
            $usu->update();
            $data = [
                "correo"     => $correo,
                'nombre'     => $usu->pernombrecompleto,
                "usuario"    => $usu->usuusuario,
                "contrasena" => $nuevaContrasena
            ];

            $pos = strpos($correo, "@gmail.com");

            if($pos){
                Mail::to($correo)->send(new MailRecuperarContrasena($data));
            }else{
                Mail::to($correo)->send(new MailRecuperarContrasenaOutlook($data));
            }
            
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, ese correo no esta registrado en Smart View";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje
        ]);

        // $data = ["contrasena" => "asd"];

        // Mail::to([
        //     'gerson.vilca@grow-analytics.com.pe',
        //     'Jose.Cruz@grow-analytics.com.pe',
        //     'Frank.Martinez@grow-analytics.com.pe'
        // ])->send(new MailRecuperarContrasena($data));

        
        // return view('CorreoRecuperarContrasena')->with($data);
    }

    public function vista()
    {
        $data = ['nombre' => 'Gerson Vilca Alvarez', "usuario" => "Gerson", "contrasena" => "1234", "correo" => "gerson@hotmail.com"];

        return view('bienvenida.CorreoBienvenidaOutlook')->with($data);
    }
}
