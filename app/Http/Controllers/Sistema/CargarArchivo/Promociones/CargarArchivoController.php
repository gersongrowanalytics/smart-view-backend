<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Promociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Illuminate\Support\Str;
use App\fecfechas;
use App\tputiposusuarios;
use App\perpersonas;
use App\usuusuarios;
use App\catcategorias;
use App\proproductos;
use App\tprtipospromociones;
use App\cancanales;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\scasucursalescategorias;
use App\csccanalessucursalescategorias;
use App\prmpromociones;
use App\cspcanalessucursalespromociones;
use App\prbpromocionesbonificaciones;
use App\prppromocionesproductos;
use App\carcargasarchivos;
use App\tuptiposusuariospermisos;

class CargarArchivoController extends Controller
{
    public function CargarArchivo(Request $request)
    {

        $respuesta      = true;
        $mensaje        = 'asd';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $cargarData = false;
        

        

        $fichero_subido = '';

        $pkid = 0;
        $log  = array(
            "NUEVA_PERSONA_EJECUTIVO"      => [],
            "NUEVA_PERSONA_CLIENTE"        => [],
            "NUEVO_USUARIO_EJECUTIVO"      => [],
            "NUEVO_USUARIO_CLIENTE"        => [],
            "NUEVO_PROMOCIONES_ASIGNDADAS" => [],
            "NUEVO_PRP_CREADO"             => [],
            "NUEVO_PRB_CREADO"             => [],
            "NUEVO_PROMOCION_CREADO"       => [],
            "NUEVO_CANAL_ASIGNADO"         => [],
            "NUEVO_CATEGORIA_ASIGNADO"     => [],
            "NUEVA_SUCURSAL"               => []
        );

        $fecActual = new \DateTime(date("Y-m-d", strtotime("2020-10-20")));


        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "log"    => $log,
        ]);
        
        
        
        return $requestsalida;

    }

    public function CargarPlanTrade(Request $request)
    {
        $log = [];

        $respuesta  = true;
        $mensaje    = 'El archivo se subio correctamente';
        $archivo    = $_FILES['file']['name'];
        $usutoken   = $request->header('api_token');
        $usuusuario = usuusuarios::where('usutoken', $usutoken)
                                ->first([
                                    'usuid',
                                ]);

        $cargarData = true;

        if($usuusuario){
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/planTrade/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $respuesta  = true;
                $cargarData = true;
            }else{
                $respuesta  = false;
                $mensaje    = 'Lo sentimos ocurrio un error el archivo no se pudo subir';
                $cargarData = false;
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 10;
            $nuevoCargaArchivo->fecid            = 8; //DIC
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $cargarData;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/promociones/planTrade/'.$archivo;
            if($nuevoCargaArchivo->save()){
                $pkid = "CAR-".$nuevoCargaArchivo->carid;
            }else{

            }
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "log"       => $log,
        ]);
        
        $descripcion = "CARGAR EXCEL DE PLAN DE TRADE DE UN EXCEL AL SISTEMA";

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            $descripcion,
            'IMPORTAR',
            '/cargarArchivo/promociones/planTrade', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;


    }
}
