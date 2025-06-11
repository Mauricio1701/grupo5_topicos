<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ColorsTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('colors')->insert([
            [
                'name' => 'Rojo',
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Azul',
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Verde',
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
