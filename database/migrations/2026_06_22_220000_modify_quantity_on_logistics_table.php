<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename existing quantity to old_quantity
        Schema::table('logistics', function (Blueprint $table) {
            $table->renameColumn('quantity', 'old_quantity');
        });

        // 2. Create the new columns
        Schema::table('logistics', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->default(0)->after('old_quantity');
            $table->string('unit')->default('pcs')->after('quantity');
        });

        // 3. Migrate data
        $logistics = DB::table('logistics')->get();
        foreach ($logistics as $logistic) {
            $oldVal = trim($logistic->old_quantity);
            // Regex to match numbers (including floats) and the unit
            preg_match('/^([\d\.]+)\s*(.*)$/', $oldVal, $matches);
            
            $quantity = 0;
            $unit = 'pcs';
            
            if (isset($matches[1]) && is_numeric($matches[1])) {
                $quantity = (float) $matches[1];
            }
            if (isset($matches[2]) && !empty(trim($matches[2]))) {
                $unit = trim($matches[2]);
            }
            
            DB::table('logistics')
                ->where('id', $logistic->id)
                ->update([
                    'quantity' => $quantity,
                    'unit' => $unit,
                ]);
        }

        // 4. Drop the old column
        Schema::table('logistics', function (Blueprint $table) {
            $table->dropColumn('old_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistics', function (Blueprint $table) {
            $table->renameColumn('quantity', 'old_quantity');
        });

        Schema::table('logistics', function (Blueprint $table) {
            $table->string('quantity')->after('old_quantity');
        });

        $logistics = DB::table('logistics')->get();
        foreach ($logistics as $logistic) {
            $qty = (float) $logistic->old_quantity;
            $unit = $logistic->unit;
            $combined = trim($qty . ' ' . $unit);

            DB::table('logistics')
                ->where('id', $logistic->id)
                ->update([
                    'quantity' => $combined,
                ]);
        }

        Schema::table('logistics', function (Blueprint $table) {
            $table->dropColumn(['old_quantity', 'unit']);
        });
    }
};
