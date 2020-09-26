<?php

use Illuminate\Database\Seeder;
use App\rtprebatetipospromociones;

class rtprebatetipospromocionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 90,
            'rtpporcentajehasta'    => 94,
            'rtpporcentajerebate'   => 1.85,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 95,
            'rtpporcentajehasta'    => 99,
            'rtpporcentajerebate'   => 2.05,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 100,
            'rtpporcentajehasta'    => 104,
            'rtpporcentajerebate'   => 2.30,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 105,
            'rtpporcentajehasta'    => 109,
            'rtpporcentajerebate'   => 2.75,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 110,
            'rtpporcentajehasta'    => 99999,
            'rtpporcentajerebate'   => 3.00,
        ]);
        



        // PARA OTRO TIPO
        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 90,
            'rtpporcentajehasta'    => 94,
            'rtpporcentajerebate'   => 1.52,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 95,
            'rtpporcentajehasta'    => 99,
            'rtpporcentajerebate'   => 1.67,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 100,
            'rtpporcentajehasta'    => 104,
            'rtpporcentajerebate'   => 1.87,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 105,
            'rtpporcentajehasta'    => 109,
            'rtpporcentajerebate'   => 2.25,
        ]);

        rtprebatetipospromociones::create([
            'fecid'                 => 1,
            'tprid'                 => 1,
            'rtpporcentajedesde'    => 110,
            'rtpporcentajehasta'    => 99999,
            'rtpporcentajerebate'   => 2.50,
        ]);

    }
}
