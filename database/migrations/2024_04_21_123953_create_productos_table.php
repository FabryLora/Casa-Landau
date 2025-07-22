<?php

use App\Models\Categoria;
use App\Models\ImagenProducto;
use App\Models\Marca;
use App\Models\MarcaProducto;
use App\Models\Materiales;
use App\Models\SubCategoria;
use App\Models\Terminaciones;
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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('order')->default("zzz");
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('medida')->nullable();
            $table->unsignedBigInteger('unidad_minima')->default(1);
            $table->unsignedInteger('descuento')->default(0);
            $table->boolean('destacado')->default(false);
            $table->foreignIdFor(Categoria::class, 'categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignIdFor(SubCategoria::class, 'sub_categoria_id')->constrained('sub_categorias')->onDelete('cascade');
            $table->foreignIdFor(Terminaciones::class, 'terminacion_id')->constrained('terminaciones')->onDelete('cascade');
            $table->foreignIdFor(Materiales::class, 'material_id')->constrained('materiales')->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
