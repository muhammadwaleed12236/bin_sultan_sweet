
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vendor_payments', 'payment_no')) {
            Schema::table('vendor_payments', function (Blueprint $table) {
                $table->string('payment_no')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendor_payments', 'payment_no')) {
            Schema::table('vendor_payments', function (Blueprint $table) {
                $table->dropColumn('payment_no');
            });
        }
    }
};
