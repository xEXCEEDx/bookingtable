<?php

// database/migrations/yyyy_mm_dd_hhmmss_add_staff_name_to_tables_and_reservations_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddStaffNameToTablesTable extends Migration
{
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            if (!Schema::hasColumn('tables', 'staff_name')) {
                $table->string('staff_name')->nullable()->after('status');
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'staff_name')) {
                $table->string('staff_name')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'staff_name')) {
                $table->dropColumn('staff_name');
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'staff_name')) {
                $table->dropColumn('staff_name');
            }
        });
    }
}
