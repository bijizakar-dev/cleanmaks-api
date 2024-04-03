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
        Schema::table('employee_absences', function (Blueprint $table) {
            $table->dropColumn('employee_schedule_id');

            $table->text('schedule')->after('image_out')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_absences', function (Blueprint $table) {
            $table->bigInteger('employee_schedule_id');

            $table->dropColumn('schedule');
        });
    }
};
