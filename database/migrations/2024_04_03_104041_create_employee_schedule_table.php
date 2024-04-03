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
        Schema::create('employee_schedule', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id');
            $table->string('day');
            $table->time('time_start');
            $table->time('time_end');
            $table->time('time_diff');
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedule');
    }
};
