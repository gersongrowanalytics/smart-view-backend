<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\Http\Controllers\Controller;
use App\usuusuarios;
use Illuminate\Http\Request;

class EliminarUsuariosController extends Controller
{
    public function EliminarUsuarios (Request $request)
    {
        $respuesta = false;
        $mensaje = "";

        $re_usuarioId = $request['re_usuarioId'];

        $usu = usuusuarios::where('usuid', $re_usuarioId)
                            ->first();

        if ($usu) {
            if ($usu->delete()) {
                $respuesta = true;
                $mensaje = "Se eliminó el usuario con éxito";
            }else{
                $respuesta = false;
                $mensaje = "Lo siento, surgió un error al eliminar el usuario";
            }
        }else{
            $respuesta = false;
            $mensaje = "Lo siento, no se encontró el registro del usuario";
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje
        ]);

        return $requestsalida;
    }
}
