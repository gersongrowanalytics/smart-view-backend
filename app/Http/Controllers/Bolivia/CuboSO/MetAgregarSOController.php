<?php

namespace App\Http\Controllers\Bolivia\CuboSO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\vsbventassobol;

class MetAgregarSOController extends Controller
{
    // EJEMPLO DE FECHA 20220901 -> vsbfecha

    // public function MetAgregarSO($anio, $mes, $dia, $limit)
    public function MetAgregarSO($fecha)
    {
        // $re_anio  = $anio;
        // $re_mes   = $mes;
        // $re_dia   = $dia;
        // $re_limit = $limit;
        
        // $datos = json_decode( file_get_contents(env('APP_URL_BOLIVIA').'/bo/mostrar-cubo-so/'.$re_anio."/".$re_mes."/".$re_dia."/".$re_limit), true );
        $datos = json_decode( file_get_contents(env('APP_URL_BOLIVIA').'/bo/mostrar-cubo-so/'.$fecha), true );
        $datas = $datos['data'];

        // vsbventassobol::where('vsbfecha', $re_anio.$re_mes.$re_dia)->delete();
        vsbventassobol::where('vsbfecha', $fecha)->delete();

        foreach($datas as $posicion => $data){

            $vsbn = new vsbventassobol;
            $vsbn->vsbmes   = $data['mes'];
            $vsbn->vsbfecha = $data['fecha'];
            $vsbn->vsbzona  = $data['zona'];
            $vsbn->vsbcajas = $data['cajas'];
            $vsbn->vsbempresa   = $data['empresa'];
            $vsbn->vsbregion    = $data['region'];
            $vsbn->vsbciudad    = $data['ciudad'];
            $vsbn->vsbmaterial  = $data['material'];
            $vsbn->vsbvendedor  = $data['vendedor'];
            $vsbn->vsbtipopago  = $data['tipo_pago'];
            $vsbn->vsbmercado   = $data['mercado'];
            $vsbn->vsbcodigosap = $data['codigo_sap'];
            $vsbn->vsbcategoria = $data['categoria'];
            $vsbn->vsbsubcategoria  = $data['subcategoria'];
            $vsbn->vsbcodigocliente = $data['codigo_cliente'];
            $vsbn->vsbtiponegocio   = $data['tipo_negocio'];
            $vsbn->vsbtotalreventa  = $data['total_reventa'];
            $vsbn->save();

        }

        return true;

    }
}
