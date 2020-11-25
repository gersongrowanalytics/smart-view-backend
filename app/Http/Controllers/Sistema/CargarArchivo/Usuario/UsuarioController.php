<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function CargarUsuarios(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);
        $fichero_subido = '';

        $pkid = 0;
        $log  = [];

        

    }
}
