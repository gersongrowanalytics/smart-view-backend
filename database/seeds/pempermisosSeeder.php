<?php

use Illuminate\Database\Seeder;
use App\pempermisos;

class pempermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        pempermisos::create([
            'pemid'     => 1,
            'pemnombre' => 'Permiso para mostrar las categorias de una promocion',
            'pemslug'   => 'promociones.mostrar.categorias',
            'pemruta'   => '/promociones/mostrar/categorias',
        ]);

        pempermisos::create([
            'pemid'     => 2,
            'pemnombre' => 'Mostrar todas las sucursales que tiene un usuario ',
            'pemslug'   => 'usuario.mostrar.sucursales',
            'pemruta'   => '/usuario/mostrar/sucursales',
        ]);

        pempermisos::create([
            'pemid'     => 3,
            'pemnombre' => 'Mostrar las ventas por tipo de promocion, con sus respectivas categorias, rebate, to go, cumplimiento, etc',
            'pemslug'   => 'ventas.mostrar',
            'pemruta'   => '/ventas/mostrar',
        ]);

        pempermisos::create([
            'pemid'     => 4,
            'pemnombre' => 'Mostrar las promociones que tiene una categoría por su canal',
            'pemslug'   => 'promociones.mostrar.promociones',
            'pemruta'   => '/promociones/mostrar/promociones',
        ]);

        pempermisos::create([
            'pemid'     => 5,
            'pemnombre' => 'MOSTRAR DATA ESPECIFICA DE PROMOCIONES PARA DESCARGAR EN EL EXCEL',
            'pemslug'   => 'promociones.descargar',
            'pemruta'   => '/promociones/descargar',
        ]);

        pempermisos::create([
            'pemid'     => 6,
            'pemnombre' => 'Editar la promocion, planchas y valorizado',
            'pemslug'   => 'promociones.editar',
            'pemruta'   => '/promociones/editar',
        ]);
        
        pempermisos::create([
            'pemid'     => 7,
            'pemnombre' => 'VER TODAS LAS ZONAS CON SUS DISTRIBUIDORAS DISPONIBLES',
            'pemslug'   => 'mostrar.sucursales.zona.todo',
            'pemruta'   => '',
        ]);

        pempermisos::create([
            'pemid'     => 8,
            'pemnombre' => 'Mostrar todas las fechas disponibles',
            'pemslug'   => 'fechas.mostrar',
            'pemruta'   => '/fechas/mostrar/fechas',
        ]);

        pempermisos::create([
            'pemid'     => 9,
            'pemnombre' => 'Mostrar los permisos disponibles de un usuario',
            'pemslug'   => 'usuario.mostrar.permisos',
            'pemruta'   => '/usuario/mostrar/permisos',
        ]);
        
        pempermisos::create([
            'pemid'     => 10,
            'pemnombre' => 'Mostrar el boton para descargar las promociones',
            'pemslug'   => 'modulo.promocion.boton.descargar.promociones',
            'pemruta'   => null,
        ]);

        // 

        pempermisos::create([
            'pemid'     => 11,
            'pemnombre' => 'MOSTRAR EL MODULO DE VENTAS',
            'pemslug'   => 'modulo.ventas',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 12,
            'pemnombre' => 'MOSTRAR EL MODULO DE PROMOCIONES',
            'pemslug'   => 'modulo.promociones',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 13,
            'pemnombre' => 'MOSTRAR EL MODULO DE GUIA',
            'pemslug'   => 'modulo.guia',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 14,
            'pemnombre' => 'MOSTRAR EL MODULO DE CARGA DE ARCHIVOS',
            'pemslug'   => 'modulo.cargar.archivos',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 15,
            'pemnombre' => 'MOSTRAR EL MODULO DE USUARIOS',
            'pemslug'   => 'modulo.usuarios',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 16,
            'pemnombre' => 'MOSTRAR EL MODULO DE REBATE',
            'pemslug'   => 'modulo.rebate',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 17,
            'pemnombre' => 'MOSTRAR EL MODULO DE TIPOS DE USUARIOS',
            'pemslug'   => 'modulo.tipos.usuarios',
            'pemruta'   => null,
        ]);

        pempermisos::create([
            'pemid'     => 18,
            'pemnombre' => 'EDITAR PROMOCION EN MODULO DE PROMOCIONES',
            'pemslug'   => 'modulo.promocion.boton.editar.promocion',
            'pemruta'   => null,
        ]);
    }
}
