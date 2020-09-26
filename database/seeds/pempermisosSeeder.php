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
            'pemnombre' => 'Permiso para mostrar las categorias de una promocion',
            'pemslug'   => 'promociones.mostrar.categorias',
            'pemruta'   => '/promociones/mostrar/categorias',
        ]);

        pempermisos::create([
            'pemnombre' => 'Mostrar todas las sucursales que tiene un usuario ',
            'pemslug'   => 'usuarios.mostrar.sucursales',
            'pemruta'   => '/usuario/mostrar/sucursales',
        ]);

        pempermisos::create([
            'pemnombre' => 'Mostrar las ventas por tipo de promocion, con sus respectivas categorias, rebate, to go, cumplimiento, etc',
            'pemslug'   => 'ventas.mostrar',
            'pemruta'   => '/ventas/mostrar',
        ]);

        pempermisos::create([
            'pemnombre' => 'Mostrar las promociones que tiene una categoría por su canal',
            'pemslug'   => 'promociones.mostrar.promociones',
            'pemruta'   => '/promociones/mostrar/promociones',
        ]);

        pempermisos::create([
            'pemnombre' => 'MOSTRAR DATA ESPECIFICA DE PROMOCIONES PARA DESCARGAR EN EL EXCEL',
            'pemslug'   => 'promociones.descargar',
            'pemruta'   => '/promociones/descargar',
        ]);

        pempermisos::create([
            'pemnombre' => 'Editar la promocion, planchas y valorizado',
            'pemslug'   => 'promociones.editar',
            'pemruta'   => '/promociones/editar',
        ]);
    }
}
