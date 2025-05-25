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
        Schema::table('vi_prod_wip_realtime', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('vi_prod_wip_realtime', 'work_equip')) {
                $table->string('work_equip')->nullable()->after('powder_type');
            }
            
            if (!Schema::hasColumn('vi_prod_wip_realtime', 'rack')) {
                $table->string('rack')->nullable()->after('work_equip');
            }
            
            if (!Schema::hasColumn('vi_prod_wip_realtime', 'facility_2')) {
                $table->string('facility_2')->nullable()->after('rack');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vi_prod_wip_realtime', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('vi_prod_wip_realtime', 'work_equip')) {
                $table->dropColumn('work_equip');
            }
            
            if (Schema::hasColumn('vi_prod_wip_realtime', 'rack')) {
                $table->dropColumn('rack');
            }
            
            if (Schema::hasColumn('vi_prod_wip_realtime', 'facility_2')) {
                $table->dropColumn('facility_2');
            }
        });
    }
};
