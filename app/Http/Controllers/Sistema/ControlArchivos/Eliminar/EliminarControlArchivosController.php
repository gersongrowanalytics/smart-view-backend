<?php

namespace App\Http\Controllers\Sistema\ControlArchivos\Eliminar;

use App\carcargasarchivos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EliminarControlArchivosController extends Controller
{
    public function EliminarControlArchivos(Request $request)
    {
        $respuesta = false;
        $mensaje = "";

        $re_carid = $request['re_carid'];

        $car = carcargasarchivos::where('carid', $re_carid)->first();
        if ($car->delete()) {
            $respuesta = true;
            $mensaje = "El registro de la lista de archivos subidos se eliminó correctamente";
            // if (unlink($car->carubicacion)) {
            //     $respuesta = true;
            //     $mensaje = "El registro y archivo de la lista de archivos subidos se eliminó correctamente";
            // }else{
            //     $respuesta = false;
            //     $mensaje = "Error al eliminar el archivo del servidor";
            // }
        }else{
            $respuesta = false;
            $mensaje = "Ingrese un id válido";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);
        
        return $requestsalida;
    }
}
