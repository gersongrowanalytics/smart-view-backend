<?php

use Illuminate\Database\Seeder;
use App\tcatiposcargasarchivos;

class tcatiposcargasarchivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Promociones'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Ventas Sell Out'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Ventas Sell in'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Objetivos Sell Out'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Objetivos Sell In'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Clientes'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de Productos'
        ]);

        tcatiposcargasarchivos::create([
            'tcanombre' => 'Carga de actualizacion de clientes'
        ]);
    }
}
