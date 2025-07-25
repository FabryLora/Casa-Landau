<?php

use App\Models\ListaDePrecios;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('password');
            $table->string("cuit");
            $table->string("direccion")->nullable();
            $table->string("provincia")->nullable();
            $table->string("localidad")->nullable();
            $table->string("rol")->default('cliente'); // Default role is 'cliente'
            $table->unsignedBigInteger('vendedor_id')->nullable();
            $table->foreign('vendedor_id')->references('id')->on('users')->nullOnDelete();
            $table->string("telefono")->nullable();
            $table->unsignedInteger("descuento_uno")->default(0);
            $table->unsignedInteger("descuento_dos")->default(0);
            $table->unsignedInteger("descuento_tres")->default(0);
            $table->foreignIdFor(ListaDePrecios::class)->nullable()
                ->constrained('lista_de_precios')
            ; // Default to the third list
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('autorizado')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
