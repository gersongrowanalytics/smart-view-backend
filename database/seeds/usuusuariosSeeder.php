<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\usuusuarios;

class usuusuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        usuusuarios::create([
            'tpuid'         => 1,
            'perid'         => 1,
            'usuusuario'    => 'Administrador',
            'usucorreo'     => 'Gerson.Vilca@grow-analytics.com',
            'usucontrasena' => Hash::make('1234'),
            // 'usutoken'      => Str::random(60),
            'usutoken'      => "HJ0t4xbw7zmQdZnpAyhffbMORIn8RpD9cUyRihQmXejkIOgmym6fuDOyWag0"
        ]);
    }
}
