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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 10)->unique();
            $table->string('lastnames', 200);
            $table->string('names', 100);
            $table->date('birthday');
            $table->string('license', 20)->nullable();
            $table->string('address', 200);
            $table->string('email', 100)->nullable();
            $table->string('photo', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('status')->default(true);
            $table->string('password');
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('employeetype');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
