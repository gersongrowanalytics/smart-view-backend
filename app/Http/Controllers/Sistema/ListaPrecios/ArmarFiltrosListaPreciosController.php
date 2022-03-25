<?php

namespace App\Http\Controllers\Sistema\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArmarFiltrosListaPreciosController extends Controller
{
    public function ArmarFiltrosListaPrecios($datas)
    {

        $arr_filtro_categorias_lp    = [];
        $arr_filtro_subcategorias_lp = [];
        $arr_filtro_formato_lp       = [];
        $arr_filtro_codsap_lp        = [];
        $arr_filtro_materiales_lp    = [];

        $encontroDataCategoria = false;
        $encontroDataSubCatego = false;
        $encontroDataFormato   = false;
        $encontroDataCodSap    = false;
        $encontroDataMaterial  = false;

        foreach($datas as $data){

            $encontroDataCategoria = false;
            $encontroDataSubCatego = false;
            $encontroDataFormato   = false;
            $encontroDataCodSap    = false;
            $encontroDataMaterial  = false;

            foreach($arr_filtro_categorias_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $dat['catnombre']){
                    $encontroDataCategoria = true;
                }

            }

            if($encontroDataCategoria == false){
                $arr_filtro_categorias_lp[] = array(
                    "data" => $data['catnombre'],
                    "seleccionado" => false
                );
            }

        }

        return array(
            "arr_filtro_categorias_lp"    => $arr_filtro_categorias_lp,
            "arr_filtro_subcategorias_lp" => $arr_filtro_subcategorias_lp,
            "arr_filtro_formato_lp"       => $arr_filtro_formato_lp,
            "arr_filtro_codsap_lp"        => $arr_filtro_codsap_lp,
            "arr_filtro_materiales_lp"    => $arr_filtro_materiales_lp,
        );

    }
}
