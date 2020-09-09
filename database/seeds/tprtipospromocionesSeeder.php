<?php

use Illuminate\Database\Seeder;
use App\tprtipospromociones;

class tprtipospromocionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tprtipospromociones::create([
            'tprnombre'         => 'Sell In',
            'tpricono'          => NULL,
            'tprcolor'          => NULL,
            'tprcolorbarra'     => '180deg, #FDA019 0%, #FCDE30 54.17%, #FDA019 100% ',
            'tprcolortooltip'   => '#FCDE30'
        ]);
        
        tprtipospromociones::create([
            'tprnombre'         => 'Sell Out',
            'tpricono'          => NULL,
            'tprcolor'          => NULL,
            'tprcolorbarra'     => '180deg, #50A78C 0%, #79E2C1 54.17%, #50A78C 100%',
            'tprcolortooltip'   => '#79E2C1'
        ]);
    }
}
