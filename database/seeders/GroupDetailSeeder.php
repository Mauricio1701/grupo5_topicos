<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GroupDetail;

class GroupDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupDetail = new GroupDetail();
        $groupDetail->scheduling_id = 1;
        $groupDetail->employee_id = 1;
        $groupDetail->save();

        $groupDetail = new GroupDetail();
        $groupDetail->scheduling_id = 1;
        $groupDetail->employee_id = 2;
        $groupDetail->save();

        $groupDetail = new GroupDetail();
        $groupDetail->scheduling_id = 1;
        $groupDetail->employee_id = 3;
        $groupDetail->save();

        $groupDetail = new GroupDetail();
        $groupDetail->scheduling_id = 2;
        $groupDetail->employee_id = 4;
        $groupDetail->save();

        $groupDetail = new GroupDetail();
        $groupDetail->scheduling_id = 2;
        $groupDetail->employee_id = 5;
        $groupDetail->save();

        $groupDetail = new GroupDetail();
        $groupDetail->scheduling_id = 2;
        $groupDetail->employee_id = 6;
        $groupDetail->save();
    }
}
