<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tuptiposusuariospermisos;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;

class PermisosController extends Controller
{
    public function EditarPermisosTipoUsuario(Request $request)
    {
        $tpuid = $request['tpuid'];
        $data  = $request['data'];
        
        $respuesta      = true;
        $mensaje        = 'Los permisos se actualizaron correctamente';
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];

        DB::beginTransaction();
        try{

            foreach($data as $dat){

                $tup = tuptiposusuariospermisos::where('pemid', $dat['pemid'])
                                            ->where('tpuid', $tpuid)
                                            ->first(['tupid']);
    
                if($tup){
    
                    if($dat->seleccionado == false){
                        if($tup->delete()){
                            $log[] = "El tup ".$tup->tupid." que le pertenecia al permiso: ".$dat['pemid']." del tipo de usaurio: ".$tpuid." se elimino correctamente";
                            $linea = __LINE__;
                        }else{
                            $log[] = "El tup ".$tup->tupid." que le pertenece al permiso: ".$dat['pemid']." del tipo de usaurio: ".$tpuid." no se pudo eliminar";
                            $respuesta = false;
                            $linea = __LINE__;
                            $mensaje = 'Lo sentimos, ocurrio un error al momento de quitar uno de los permisos';
                        }
                    }
    
                }else{
                    if($dat->seleccionado == true){
                        $nuevoTup = new tuptiposusuariospermisos;
                        $nuevoTup->pemid = $dat['pemid'];
                        $nuevoTup->tpuid = $tpuid;
                        if($nuevoTup->save()){
                            $log[] = "El tup ".$nuevoTup->tupid." que le pertenece al permiso: ".$dat['pemid']." del tipo de usaurio: ".$tpuid." se creo satisfactoriamente";
                            $linea = __LINE__;
                        }else{
                            $log[] = "El tup ".$nuevoTup->tupid." que le pertenece al permiso: ".$dat['pemid']." del tipo de usaurio: ".$tpuid." no se pudo crear";
                            $respuesta = false;
                            $linea = __LINE__;
                            $mensaje = 'Lo sentimos, ocurrio un error al momento agregar un permiso';
                        }
                    }
                }
    
            }    

            DB::commit();

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[] = "ERROR DE SERVIDOR: ".$mensajedev;
            DB::rollBack();
        }
        
        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Editar los permisos que tiene un tipo de usuario',
            'EDITAR',
            '/configuracion/usuarios/editar/permisos/tipoUsuario', //ruta
            'TPU-'.$tpuid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
