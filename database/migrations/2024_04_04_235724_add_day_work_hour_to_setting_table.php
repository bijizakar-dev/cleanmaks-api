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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('sunday_type')->nullable();
            $table->time('sunday_in')->nullable();
            $table->time('sunday_out')->nullable();
            $table->time('sunday_total')->nullable();

            $table->boolean('monday_type')->nullable();
            $table->time('monday_in')->nullable();
            $table->time('monday_out')->nullable();
            $table->time('monday_total')->nullable();

            $table->boolean('tuesday_type')->nullable();
            $table->time('tuesday_in')->nullable();
            $table->time('tuesday_out')->nullable();
            $table->time('tuesday_total')->nullable();

            $table->boolean('wednesday_type')->nullable();
            $table->time('wednesday_in')->nullable();
            $table->time('wednesday_out')->nullable();
            $table->time('wednesday_total')->nullable();

            $table->boolean('thursday_type')->nullable();
            $table->time('thursday_in')->nullable();
            $table->time('thursday_out')->nullable();
            $table->time('thursday_total')->nullable();

            $table->boolean('friday_type')->nullable();
            $table->time('friday_in')->nullable();
            $table->time('friday_out')->nullable();
            $table->time('friday_total')->nullable();

            $table->boolean('saturday_type')->nullable();
            $table->time('saturday_in')->nullable();
            $table->time('saturday_out')->nullable();
            $table->time('saturday_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('sunday_type')->nullable();
            $table->dropColumn('sunday_in')->nullable();
            $table->dropColumn('sunday_out')->nullable();
            $table->dropColumn('sunday_total')->nullable();

            $table->dropColumn('monday_type')->nullable();
            $table->dropColumn('monday_in')->nullable();
            $table->dropColumn('monday_out')->nullable();
            $table->dropColumn('monday_total')->nullable();

            $table->dropColumn('tuesday_type')->nullable();
            $table->dropColumn('tuesday_in')->nullable();
            $table->dropColumn('tuesday_out')->nullable();
            $table->dropColumn('tuesday_total')->nullable();

            $table->dropColumn('wednesday_type')->nullable();
            $table->dropColumn('wednesday_in')->nullable();
            $table->dropColumn('wednesday_out')->nullable();
            $table->dropColumn('wednesday_total')->nullable();

            $table->dropColumn('thursday_type')->nullable();
            $table->dropColumn('thursday_in')->nullable();
            $table->dropColumn('thursday_out')->nullable();
            $table->dropColumn('thursday_total')->nullable();

            $table->dropColumn('friday_type')->nullable();
            $table->dropColumn('friday_in')->nullable();
            $table->dropColumn('friday_out')->nullable();
            $table->dropColumn('friday_total')->nullable();

            $table->dropColumn('saturday_type')->nullable();
            $table->dropColumn('saturday_in')->nullable();
            $table->dropColumn('saturday_out')->nullable();
            $table->dropColumn('saturday_total')->nullable();
        });
    }
};
