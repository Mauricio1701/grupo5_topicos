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
        Schema::create('changedetails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('change_id');
            $table->foreign('change_id')->references('id')->on('changes');
            $table->unsignedBigInteger('scheduling_id');
            $table->foreign('scheduling_id')->references('id')->on('scheduling');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('changedetails');
    }
};
