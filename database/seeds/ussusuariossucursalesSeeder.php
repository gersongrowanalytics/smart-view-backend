<?php

use Illuminate\Database\Seeder;
use App\ussusuariossucursales;

class ussusuariossucursalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ussusuariossucursales::create([
            'usuid' => 1,
            'sucid' => 1,
        ]);
    }
}
