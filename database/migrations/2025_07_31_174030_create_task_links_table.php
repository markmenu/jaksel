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
        // Tabel ini akan menyimpan hubungan dependensi antar tugas
        Schema::create('task_links', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Tipe hubungan (misal: finish_to_start)
            $table->unsignedBigInteger('source_task_id'); // ID tugas sumber
            $table->unsignedBigInteger('target_task_id'); // ID tugas target
            $table->timestamps();

            // Menambahkan foreign key constraint (opsional tapi direkomendasikan)
            $table->foreign('source_task_id')->references('id')->on('kegiatans')->onDelete('cascade');
            $table->foreign('target_task_id')->references('id')->on('kegiatans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_links');
    }
};
