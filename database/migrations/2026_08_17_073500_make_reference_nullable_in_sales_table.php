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
        Schema::table('sales', function (Blueprint $table) {
            $table->text('reference')->nullable()->change();
            $table->text('brand')->nullable()->change();
            $table->text('unit')->nullable()->change();
            $table->text('total_amount_Words')->nullable()->change();
            $table->text('color')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->text('reference')->nullable(false)->change();
        });
    }
};
