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
        Schema::create('employee_absences', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->foreignId('user_id');
            $table->foreignId('employee_id');
            $table->foreignId('employee_schedule_id')->nullable();

            $table->datetime('clock_in')->nullable();
            $table->string('location_in')->nullable();
            $table->string('latitude_longitude_in')->nullable();
            $table->string('image_in')->nullable();

            $table->datetime('clock_out')->nullable();
            $table->string('location_out')->nullable();
            $table->string('latitude_longitude_out')->nullable();
            $table->string('image_out')->nullable();

            $table->time('total_hour')->nullable();
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_absences');
    }
};
