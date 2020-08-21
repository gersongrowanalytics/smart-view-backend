<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('tditiposdocumentosidentidadesSeeder');
        $this->call('perpersonasSeeder');
        $this->call('tputiposusuariosSeeder');
        $this->call('usuusuariosSeeder');
        
    }
}
