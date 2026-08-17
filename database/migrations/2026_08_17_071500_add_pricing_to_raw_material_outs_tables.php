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
        Schema::table('raw_material_outs', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_material_outs', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('taken_by');
            }
        });

        Schema::table('raw_material_out_items', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_material_out_items', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->default(0)->after('qty');
            }
            if (!Schema::hasColumn('raw_material_out_items', 'line_total')) {
                $table->decimal('line_total', 15, 2)->default(0)->after('unit_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_out_items', function (Blueprint $table) {
            if (Schema::hasColumn('raw_material_out_items', 'line_total')) {
                $table->dropColumn('line_total');
            }
            if (Schema::hasColumn('raw_material_out_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });

        Schema::table('raw_material_outs', function (Blueprint $table) {
            if (Schema::hasColumn('raw_material_outs', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
