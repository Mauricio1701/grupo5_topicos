<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('new_employee_id');
            $table->foreign('new_employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('old_employee_id');
            $table->foreign('old_employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('new_vehicle_id');
            $table->foreign('new_vehicle_id')->references('id')->on('vehicles');
            $table->unsignedBigInteger('old_vehicle_id');
            $table->foreign('old_vehicle_id')->references('id')->on('vehicles');
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->unsignedBigInteger('reason_id');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->date('change_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('changes');
    }
};
