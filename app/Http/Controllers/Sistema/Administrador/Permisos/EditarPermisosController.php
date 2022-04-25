<?php

namespace App\Http\Controllers\Sistema\Administrador\Permisos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\pempermisos;
use App\tuptiposusuariospermisos;

class EditarPermisosController extends Controller
{
    public function EditarPermisos(Request $request)
    {

        $respuesta = true;
        $mensaje = "El permiso se edito correctamente";

        $re_pemid  = $request['pemid'];
        $re_nombre = $request['nombreEditando'];
        $re_slug   = $request['slugEditado'];
        $re_ruta   = $request['rutaEditada'];

        $pem = pempermisos::find($re_pemid);

        if($pem){

            $pem->pemnombre = $re_nombre;
            $pem->pemslug   = $re_slug;
            $pem->pemruta   = $re_ruta;
            $pem->update();

        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no encontramos el permiso seleccionado";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje
        ]);

        return $requestsalida;
    }

    public function EditarEliminarPermiso(Request $request)
    {

        $respuesta = true;
        $mensaje = "El permiso se edito correctamente";

        $re_pemid = $request['pemid'];

        $tup = tuptiposusuariospermisos::where('pemid', $re_pemid)
                                        ->first();

        if($tup){

            $pem = pempermisos::find($re_pemid);

            if($pem){
                
                $pem->estid = 2;
                $pem->update();

            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos, no encontramos el permiso seleccionado";   
            }


        }else{

            $pem = pempermisos::find($re_pemid);

            if($pem){
                
                $pem->delete();

            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos, no encontramos el permiso seleccionado";   
            }

        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje
        ]);

        return $requestsalida;
    }
}
