<?php

use Illuminate\Database\Seeder;
use App\tuptiposusuariospermisos;

class tuptiposusuariospermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tuptiposusuariospermisos::create([
            'pemid' => 1,
            'tpuid' => 2
        ]);

        tuptiposusuariospermisos::create([
            'pemid' => 2,
            'tpuid' => 2
        ]);

        tuptiposusuariospermisos::create([
            'pemid' => 3,
            'tpuid' => 2
        ]);

        tuptiposusuariospermisos::create([
            'pemid' => 4,
            'tpuid' => 2
        ]);

        tuptiposusuariospermisos::create([
            'pemid' => 5,
            'tpuid' => 2
        ]);

        tuptiposusuariospermisos::create([
            'pemid' => 6,
            'tpuid' => 2
        ]);
    }
}
