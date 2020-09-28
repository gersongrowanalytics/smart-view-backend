<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tuptiposusuariospermisos;
use App\pempermisos;

class PermisosController extends Controller
{
    public function MostrarPermisosTipoUsuario(Request $request) //MOSTRAR PERMISOS DE UN TIPO DE USUARIO
    {

        $tpuid = $request['tpuid'];

        $tups = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                        ->where('tuptiposusuariospermisos.tpuid', $tpuid)
                                        ->get([
                                            'pem.pemid',
                                            'pem.pemnombre',
                                            'pem.pemslug',
                                            'pem.pemruta'
                                        ]);
        
        
        $pems = pempermisos::get(['pemid', 'pemnombre', 'pemslug', 'pemruta']);

        foreach($pems as $posicionPem => $pem){
            foreach($tups as $tup){
                if($pem->pemid == $tup->pemid){
                    $pems[$posicionPem]['seleccionado'] = true;
                    break;
                }else{
                    $pems[$posicionPem]['seleccionado'] = false;
                }
            }
        }

        $requestsalida = response()->json([
            "respuesta" => true,
            "mensaje"   => 'Los permisos se cargaron satisfactoriamente',
            "datos"     => $pems
        ]);
        
        return $requestsalida;
    }
}
