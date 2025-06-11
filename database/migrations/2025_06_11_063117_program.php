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
        //
        schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('gambar_program')->nullable();
            $table->string('nama_program');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('programs');
    }
};
