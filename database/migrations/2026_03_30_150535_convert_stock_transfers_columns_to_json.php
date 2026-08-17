<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Remove any foreign key attached to product_id
         */
        $foreignKeys = DB::select("
            SELECT
                CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'stock_transfers'
              AND COLUMN_NAME = 'product_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $foreignKey) {
            DB::statement(
                "ALTER TABLE `stock_transfers` DROP FOREIGN KEY `{$foreignKey->CONSTRAINT_NAME}`"
            );
        }

        /*
         * Remove indexes on product_id.
         * LONGTEXT cannot be indexed without a prefix length.
         */
        $indexes = DB::select("
            SELECT
                DISTINCT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'stock_transfers'
              AND COLUMN_NAME = 'product_id'
              AND INDEX_NAME != 'PRIMARY'
        ");

        foreach ($indexes as $index) {
            DB::statement(
                "ALTER TABLE `stock_transfers` DROP INDEX `{$index->INDEX_NAME}`"
            );
        }

        /*
         * Convert product_id and quantity to LONGTEXT
         */
        DB::statement("
            ALTER TABLE `stock_transfers`
            MODIFY `product_id` LONGTEXT NOT NULL,
            MODIFY `quantity` LONGTEXT NOT NULL
        ");
    }

    public function down(): void
    {
        /*
         * Convert columns back
         */
        DB::statement("
            ALTER TABLE `stock_transfers`
            MODIFY `product_id` BIGINT UNSIGNED NOT NULL,
            MODIFY `quantity` INT NOT NULL
        ");

        /*
         * Restore foreign key
         */
        DB::statement("
            ALTER TABLE `stock_transfers`
            ADD CONSTRAINT `stock_transfers_product_id_foreign`
            FOREIGN KEY (`product_id`)
            REFERENCES `products` (`id`)
            ON DELETE CASCADE
        ");
    }
};
