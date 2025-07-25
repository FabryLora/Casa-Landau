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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->string('mail')->nullable();
            $table->string('mail_info')->nullable();
            $table->string('mail_pedidos')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('location_dos')->nullable();
            $table->string('wp')->nullable();
            $table->string('fb')->nullable();
            $table->string('ig')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
