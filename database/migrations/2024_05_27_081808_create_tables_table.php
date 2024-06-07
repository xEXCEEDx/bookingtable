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
            $table->id();
            $table->integer('table_number')->unique();
            $table->string('status')->default('available');
            $table->timestamps();
        });

        $this->populateTableNumbers();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tables');
    }

    /**
     * Populate the tables with initial data.
     */
    protected function populateTableNumbers()
    {
        $numberOfTables = 10;

        for ($i = 1; $i <= $numberOfTables; $i++) {
            \App\Models\Table::create([
                'table_number' => $i,
                'status' => 'available',
            ]);
        }
    }
};
