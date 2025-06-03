<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsTableSeeder extends Seeder
{
   public function run()
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('colors')->truncate();

    // Aquí el código para insertar datos nuevos
    DB::table('colors')->insert([
        ['name' => 'Red'],
        ['name' => 'Blue'],
        ['name' => 'Green'],
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}


}
