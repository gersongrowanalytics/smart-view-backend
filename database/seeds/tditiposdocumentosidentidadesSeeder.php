<?php

use Illuminate\Database\Seeder;
use App\tditiposdocumentosidentidades;

class tditiposdocumentosidentidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tditiposdocumentosidentidades::create([
            'tdiabreviacion' => 'DNI',
            'tdinombre'      => 'DOCUMENTO NACIONAL DE IDENTIDAD'
        ]);

        tditiposdocumentosidentidades::create([
            'tdiabreviacion' => 'RUC',
            'tdinombre'      => 'REGISTRO UNICO DEL CONTRIBUYENTE'
        ]);
    }
}
