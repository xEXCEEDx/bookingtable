<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if the 'staff_name' column does not exist before adding it
        if (!Schema::hasColumn('reservations', 'staff_name')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->date('reservation_date')->nullable()->default(now())->after('status');
                $table->string('staff_name')->nullable()->after('status');
            });
        }
    }

    public function down()
    {
        // Drop the 'staff_name' column if it exists
        if (Schema::hasColumn('reservations', 'staff_name')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('staff_name');
                $table->dropColumn('reservation_date');
            });

        }
    }
};
