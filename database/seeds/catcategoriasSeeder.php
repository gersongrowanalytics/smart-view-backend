<?php

use Illuminate\Database\Seeder;
use App\catcategorias;

class catcategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        catcategorias::create([
            'catnombre'                  => 'Family Care',
            'catimagenfondo'             => env('APP_URL').'/Sistema/categorias/img/fondos/familyf.png',
            'caticono'                   => env('APP_URL').'/Sistema/categorias/img/iconos/nfamily.png',
            'catcolorhover'              => '12, 53, 98, 0.85',
            'catcolor'                   => '#0C3562',
            'caticonoseleccionado'       => env('APP_URL').'/Sistema/categorias/img/iconos/familySeleccionado.png',
            'caticonohover'              => env('APP_URL').'/Sistema/categorias/img/iconos-hover/family.png',
            'catimagenfondoseleccionado' => env('APP_URL').'/Sistema/categorias/img/fondos-seleccionados/family.png',
            'catimagenfondoopaco'        => env('APP_URL').'/Sistema/categorias/img/fondos-opacos/family.png'
        ]);

        catcategorias::create([
            'catnombre'                  => 'Infant Care',
            'catimagenfondo'             => env('APP_URL').'/Sistema/categorias/img/fondos/infantf.png',
            'caticono'                   => env('APP_URL').'/Sistema/categorias/img/iconos/ninfant.png',
            'catcolorhover'              => '228, 26, 55, 0.85',
            'catcolor'                   => '#E41A37',
            'caticonoseleccionado'       => env('APP_URL').'/Sistema/categorias/img/iconos/infantSeleccionado.png',
            'caticonohover'              => env('APP_URL').'/Sistema/categorias/img/iconos-hover/infant.png',
            'catimagenfondoseleccionado' => env('APP_URL').'/Sistema/categorias/img/fondos-seleccionados/infant.png',
            'catimagenfondoopaco'        => env('APP_URL').'/Sistema/categorias/img/fondos-opacos/infant.png'
        ]);

        catcategorias::create([
            'catnombre'                  => 'Adult Care',
            'catimagenfondo'             => env('APP_URL').'/Sistema/categorias/img/fondos/adultf.png',
            'caticono'                   => env('APP_URL').'/Sistema/categorias/img/iconos/nadult.png',
            'catcolorhover'              => '135, 103, 150, 0.85',
            'catcolor'                   => '#876796',
            'caticonoseleccionado'       => env('APP_URL').'/Sistema/categorias/img/iconos/adultSeleccionado.png',
            'caticonohover'              => env('APP_URL').'/Sistema/categorias/img/iconos-hover/adult.png',
            'catimagenfondoseleccionado' => env('APP_URL').'/Sistema/categorias/img/fondos-seleccionados/adult.png',
            'catimagenfondoopaco'        => env('APP_URL').'/Sistema/categorias/img/fondos-opacos/adult.png'
        ]);

        catcategorias::create([
            'catnombre'                  => 'Wipes',
            'catimagenfondo'             => env('APP_URL').'/Sistema/categorias/img/fondos/wipesf.png',
            'caticono'                   => env('APP_URL').'/Sistema/categorias/img/iconos/nwipes.png',
            'catcolorhover'              => '145, 197, 89, 0.85',
            'catcolor'                   => '#91C559',
            'caticonoseleccionado'       => env('APP_URL').'/Sistema/categorias/img/iconos/wipesSeleccionado.png',
            'caticonohover'              => env('APP_URL').'/Sistema/categorias/img/iconos-hover/wipes.png',
            'catimagenfondoseleccionado' => env('APP_URL').'/Sistema/categorias/img/fondos-seleccionados/wipes.png',
            'catimagenfondoopaco'        => env('APP_URL').'/Sistema/categorias/img/fondos-opacos/wipes.png'
        ]);

        catcategorias::create([
            'catnombre'                  => 'Fem Care',
            'catimagenfondo'             => env('APP_URL').'/Sistema/categorias/img/fondos/femf.png',
            'caticono'                   => env('APP_URL').'/Sistema/categorias/img/iconos/nfem.png',
            'catcolorhover'              => '178, 27, 134, 0.85',
            'catcolor'                   => '#B21B86',
            'caticonoseleccionado'       => env('APP_URL').'/Sistema/categorias/img/iconos/femSeleccionado.png',
            'caticonohover'              => env('APP_URL').'/Sistema/categorias/img/iconos-hover/fem.png',
            'catimagenfondoseleccionado' => env('APP_URL').'/Sistema/categorias/img/fondos-seleccionados/fem.png',
            'catimagenfondoopaco'        => env('APP_URL').'/Sistema/categorias/img/fondos-opacos/fem.png'
        ]);

        catcategorias::create([
            'catnombre'                  => 'MultiCategoria',
            'catimagenfondo'             => env('APP_URL').'/Sistema/categorias/img/fondos/multicategoriaf.png',
            'caticono'                   => env('APP_URL').'/Sistema/categorias/img/iconos/nmulticategoria.png',
            'catcolorhover'              => '55, 138, 192, 0.85',
            'catcolor'                   => '#378AC0',
            'caticonoseleccionado'       => env('APP_URL').'/Sistema/categorias/img/iconos/multicategoriaSeleccionado.png',
            'caticonohover'              => env('APP_URL').'/Sistema/categorias/img/iconos-hover/multicategoria.png',
            'catimagenfondoseleccionado' => env('APP_URL').'/Sistema/categorias/img/fondos-seleccionados/multicategoria.png',
            'catimagenfondoopaco'        => null
        ]);


    }
}
