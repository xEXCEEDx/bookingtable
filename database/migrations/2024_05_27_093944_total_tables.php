<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->integer('total_tables')->nullable()->after('status');
        });

        // Populate initial table numbers with total_tables
        $this->populateTableNumbers();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('total_tables');
        });
    }

    protected function populateTableNumbers()
    {
        // Assuming you want to create a certain number of tables
        $numberOfTables = 10; // Change this number to how many tables you want to create initially

        for ($i = 1; $i <= $numberOfTables; $i++) {
            \App\Models\Table::create(['table_number' => $i, 'status' => 'available', 'total_tables' => $numberOfTables]);
        }
    }
};
