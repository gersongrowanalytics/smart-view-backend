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
        $this->call('estestadosSeeder');
        $this->call('tditiposdocumentosidentidadesSeeder');
        $this->call('perpersonasSeeder');
        $this->call('tputiposusuariosSeeder');
        $this->call('usuusuariosSeeder');
        $this->call('pempermisosSeeder');
        $this->call('sucsucursalSeeder');
        $this->call('ussusuariossucursalesSeeder');
        $this->call('tuptiposusuariospermisosSeeder');
        $this->call('catcategoriasSeeder');
        $this->call('tprtipospromocionesSeeder');
        $this->call('fecfechasSeeder');
        $this->call('tcatiposcargasarchivosSeeder');
        $this->call('tretiposrebatesSeeder');
        $this->call('rtprebatetipospromocionesSeeder');
        $this->call('trrtiposrebatesrebatesSeeder');
    }
}
