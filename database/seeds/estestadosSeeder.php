<?php

use Illuminate\Database\Seeder;
use App\estestados;

class estestadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        estestados::create([
            'estid'          => 1,
            'estnombre'      => 'ACTIVADO',
            'estdescripcion' => 'CUANDO UN ITEM ESTA ACTIVO O EN USO'
        ]);

        estestados::create([
            'estid'          => 2,
            'estnombre'      => 'DESACTIVADO',
            'estdescripcion' => 'CUANDO UN ITEM ESTA DESACTIVADO'
        ]);
    }
}
