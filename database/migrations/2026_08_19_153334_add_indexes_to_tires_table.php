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
        Schema::table('tires', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_promoted');
            $table->index('brand_id');
            $table->index('category_id');
            $table->index('width');
            $table->index('profile');
            $table->index('rim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tires', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_promoted']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['width']);
            $table->dropIndex(['profile']);
            $table->dropIndex(['rim']);
        });
    }
};
