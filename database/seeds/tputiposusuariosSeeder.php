<?php

use Illuminate\Database\Seeder;
use App\tputiposusuarios;

class tputiposusuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tputiposusuarios::create([
            'tpunombre' => 'Administrador',
            'tpuprivilegio' => 'todo'
        ]);

        tputiposusuarios::create([
            'tpunombre' => 'Cliente',
            'tpuprivilegio' => null
        ]);

        tputiposusuarios::create([
            'tpunombre' => 'Ejecutivo',
            'tpuprivilegio' => null
        ]);
    }
}
