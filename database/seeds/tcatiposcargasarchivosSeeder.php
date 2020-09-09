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
            'tcanombre' => 'Carga de Ventas'
        ]);
    }
}
