<?php

use Illuminate\Database\Seeder;
use App\fecfechas;

class fecfechasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        fecfechas::create([
            'fecfecha' => '2020-08-01',
            'fecdia'   => '01',
            'fecmes'   => 'AGO',
            'fecano'   => '2020',
        ]);
    }
}
