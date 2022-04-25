<?php

namespace App\Http\Controllers\Sistema\Administrador\Permisos;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\pempermisos;
use App\tpetipopermiso;
use Illuminate\Http\Request;

class CrearPermisosController extends Controller
{
    public function CrearPermisos(Request $request)
    {
        $respuesta = false;
        $mensaje   = '';
        $pkid      = [];
        $log       = [];

        $usutoken  = $request->header('api_token');

        // $categoria = $request['categoria_id'];
        $categoriaNombre = $request['categoriaNueva'];
        $permiso   = $request['permiso'];
        $slug      = $request['slug'];
        $ruta      = $request['ruta'];

        $existencia_pem = pempermisos::where('pemnombre', $permiso)
                                        ->where('pemslug', $slug)
                                        ->where('pemruta', $ruta)
                                        // ->where('tpeid', $categoria)
                                        ->first(['pemid']);
                                    
        if (!$existencia_pem) {

            $tpeid = 0;

            if(isset($categoriaNombre)){

                $tpe = tpetipopermiso::where('tpenombre', $categoriaNombre)
                                        ->first();

                if($tpe){
                    $tpeid = $tpe->tpeid;
                }else{
                    $tpen = new tpetipopermiso;
                    $tpen->tpenombre = $categoriaNombre;
                    if($tpen->save()){
                        $tpeid = $tpen->tpeid;
                    }
                    
                }

            }else{ 
                $tpeid = 6; // OTROS
            }

            $pemn = new pempermisos();
            $pemn->tpeid = $tpeid;
            $pemn->pemnombre = $permiso;
            $pemn->pemslug = $slug;
            $pemn->pemruta = $ruta;
            if ($pemn->save()) {
                $respuesta = true;
                $mensaje   = 'Se registro existosamente el permiso';
                $log[] = "El permiso se registro correctamente, pemid: ".$pemn->pemid;
            }else{
                $respuesta = false;
                $mensaje   = 'Lo siento, hubo un error al registrar el permiso';
                $log[]     = 'No se pudo registrar el permiso';
            }
        }else{
            $pemn = [];
            $respuesta = false;
            $mensaje   = 'Lo siento, los datos del permiso que ingreso ya se encuentran registrados';
            $log[]     = 'Ya se registro anteriormente el permiso';
        }
        
        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $pemn
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Crear nuevo permiso',
            'CREAR',
            '/administrativo/permisos/crear/permiso', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }

        return $requestsalida;
    }
}
