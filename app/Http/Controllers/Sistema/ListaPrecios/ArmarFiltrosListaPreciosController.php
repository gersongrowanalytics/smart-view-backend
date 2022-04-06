<?php

namespace App\Http\Controllers\Sistema\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArmarFiltrosListaPreciosController extends Controller
{
    public function ArmarFiltrosListaPrecios($datas)
    {

        $arr_filtro_customer_group_lp    = [];
        $arr_filtro_categorias_lp    = [];
        $arr_filtro_subcategorias_lp = [];
        $arr_filtro_formato_lp       = [];
        $arr_filtro_codsap_lp        = [];
        $arr_filtro_materiales_lp    = [];

        $encontroDataCustomerGroup = false;
        $encontroDataCategoria = false;
        $encontroDataSubCatego = false;
        $encontroDataFormato   = false;
        $encontroDataCodSap    = false;
        $encontroDataMaterial  = false;

        foreach($datas as $data){

            $encontroDataCustomerGroup = false;
            $encontroDataCategoria = false;
            $encontroDataSubCatego = false;
            $encontroDataFormato   = false;
            $encontroDataCodSap    = false;
            $encontroDataMaterial  = false;

            foreach($arr_filtro_customer_group_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $data['trenombre']){
                    $encontroDataCustomerGroup = true;
                }

            }

            if($encontroDataCustomerGroup == false){
                $arr_filtro_customer_group_lp[] = array(
                    "data" => $data['trenombre'],
                    "seleccionado" => true
                );
            }

            // CATEGORIAS

            foreach($arr_filtro_categorias_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $data['catnombre']){
                    $encontroDataCategoria = true;
                }

            }

            if($encontroDataCategoria == false){
                $arr_filtro_categorias_lp[] = array(
                    "data" => $data['catnombre'],
                    "seleccionado" => true
                );
            }

            // FORMATOS

            foreach($arr_filtro_formato_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $data['proformato']){
                    $encontroDataFormato = true;
                }

            }

            if($encontroDataFormato == false){
                $arr_filtro_formato_lp[] = array(
                    "data" => $data['proformato'],
                    "seleccionado" => true
                );
            }

            // SUBCATEGORIAS

            foreach($arr_filtro_subcategorias_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $data['ltpsubcategoria']){
                    $encontroDataSubCatego = true;
                }

            }

            if($encontroDataSubCatego == false){
                $arr_filtro_subcategorias_lp[] = array(
                    "data" => $data['ltpsubcategoria'],
                    "seleccionado" => true
                );
            }

            // COD SAP

            foreach($arr_filtro_codsap_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $data['ltpcodigosap']){
                    $encontroDataCodSap = true;
                }

            }

            if($encontroDataCodSap == false){
                $arr_filtro_codsap_lp[] = array(
                    "data" => $data['ltpcodigosap'],
                    "seleccionado" => true
                );
            }

             // MATERIAL

             foreach($arr_filtro_materiales_lp as $arr_filtro){
                
                if($arr_filtro['data'] == $data['pronombre']){
                    $encontroDataMaterial = true;
                }

            }

            if($encontroDataMaterial == false){
                $arr_filtro_materiales_lp[] = array(
                    "data" => $data['pronombre'],
                    "seleccionado" => true
                );
            }

        }

        return array(
            "arr_filtro_customer_group_lp" => $arr_filtro_customer_group_lp,
            "arr_filtro_categorias_lp"    => $arr_filtro_categorias_lp,
            "arr_filtro_subcategorias_lp" => $arr_filtro_subcategorias_lp,
            "arr_filtro_formato_lp"       => $arr_filtro_formato_lp,
            "arr_filtro_codsap_lp"        => $arr_filtro_codsap_lp,
            "arr_filtro_materiales_lp"    => $arr_filtro_materiales_lp,
        );

    }
}
