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
        Schema::create('permits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id_applicant'); //ID Karyawan Pemohon
            $table->dateTime('date'); // Tanggal permohonan
            $table->String('type'); // jenis cuti
            $table->date('start_date'); // tanggal awal cuti
            $table->date('end_date'); // akhir tanggal cuti
            $table->integer('total'); // akhir tanggal cuti
            $table->String('reason'); // alasan cuti
            $table->String('image')->nullable(); // file pendukung cuti
            $table->String('status'); // status permohonan
            $table->foreignId('user_id_decide')->nullable(); // user id yang memberikan persetujuan
            $table->dateTime('verified_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permits');
    }
};
