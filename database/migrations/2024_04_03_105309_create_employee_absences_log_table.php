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
        Schema::create('employee_absences_log', function (Blueprint $table) {
            $table->id();

            $table->datetime('date');
            $table->foreignId('employee_absences_id');
            $table->enum('absence', ['IN', 'OUT']);
            $table->string('type')->nullable();
            $table->string('device_info')->nullable();
            $table->foreignId('user_id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_absences_log');
    }
};
