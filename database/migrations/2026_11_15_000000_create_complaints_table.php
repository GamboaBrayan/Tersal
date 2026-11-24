<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('generated_id')->unique();
            $table->boolean('es_menor')->default(false);
            $table->string('nombre_apoderado')->nullable();
            $table->string('tipo_documento', 20);
            $table->string('numero_documento', 20);
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('telefono', 50);
            $table->string('email');
            $table->string('direccion');
            $table->string('distrito');
            $table->string('tipo_bien', 50);
            $table->decimal('monto_reclamado', 12, 2);
            $table->text('descripcion_bien');
            $table->string('tipo_reclamo', 50);
            $table->text('detalle');
            $table->text('pedido')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

