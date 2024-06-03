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
        Schema::create('tables', function (Blueprint $table) {
            $table->id(); // This will be the auto-increment primary key
            $table->integer('table_number')->unique(); // Ensure table_number is unique
            $table->string('status')->default('available');
            $table->timestamps();
        });

        // Populate initial table numbers starting from 1
        $this->populateTableNumbers();
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }

    protected function populateTableNumbers()
    {
        // Assuming you want to create a certain number of tables
        $numberOfTables = 10; // Change this number to how many tables you want to create initially

        for ($i = 1; $i <= $numberOfTables; $i++) {
            \App\Models\Table::create(['table_number' => $i, 'status' => 'available']);
        }
    }
};
