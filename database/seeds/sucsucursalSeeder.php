<?php

use Illuminate\Database\Seeder;
use App\sucsucursales;

class sucsucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sucsucursales::create([
            'sucnombre' => 'Sucursal A',
        ]);
    }
}
