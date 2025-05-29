<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $S1 = new Shift();
        $S1->name = 'MAÑANA';
        $S1->save();

        $S2 = new Shift();
        $S2->name = 'TARDE';
        $S2->save();

        $S3 = new Shift();
        $S3->name = 'NOCTURNO';
        $S3->save();
    }
}
