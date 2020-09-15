<?php

use Illuminate\Database\Seeder;
use App\tretiposrebates;

class tretiposrebatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tretiposrebates::create([
            'treid'     => 1,
            'trenombre' => 'U9'
        ]);

        tretiposrebates::create([
            'treid'     => 2,
            'trenombre' => 'UJ'
        ]);
        
        tretiposrebates::create([
            'treid'     => 3,
            'trenombre' => 'UB'
        ]);

        tretiposrebates::create([
            'treid'     => 4,
            'trenombre' => 'UD'
        ]);
    }
}
