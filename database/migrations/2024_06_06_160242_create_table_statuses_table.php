<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

        public function up()
        {
            Schema::create('table_statuses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
                $table->string('status');
                $table->date('date');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down()
        {
            Schema::dropIfExists('table_statuses');
        }
    };
