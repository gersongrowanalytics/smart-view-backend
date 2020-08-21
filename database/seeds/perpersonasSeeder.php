<?php

use Illuminate\Database\Seeder;
use App\perpersonas;

class perpersonasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        perpersonas::create([
            'tdiid' => 1,
            'pernumerodocumentoidentidad'   => '73819654',
            'pernombre'          => 'GERSON',
            'perapellidopaterno' => 'VILCA',
            'perapellidomaterno' => 'ALVAREZ',
        ]);
    }
}
