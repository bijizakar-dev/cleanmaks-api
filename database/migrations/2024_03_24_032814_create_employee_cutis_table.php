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
        Schema::create('employee_cutis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id'); //ID Karyawan
            $table->integer('quota')->default(0);
            $table->integer('quota_used')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_cutis');
    }
};
