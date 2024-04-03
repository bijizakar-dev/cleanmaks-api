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
        Schema::table('employee_schedule', function (Blueprint $table) {
            $table->time('time_diff')->nullable()->change();
            $table->time('time_start')->nullable()->change();
            $table->time('time_end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_schedule', function (Blueprint $table) {
            $table->time('working_hour')->change();
            $table->time('working_hour')->change();
            $table->time('working_hour')->change();

        });
    }
};
