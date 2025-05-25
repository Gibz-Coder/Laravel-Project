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
        Schema::create('vi_prod_wip_realtime', function (Blueprint $table) {
            $table->string('no')->nullable();
            $table->string('site')->nullable();
            $table->string('facility')->nullable();
            $table->string('major_process')->nullable();
            $table->string('sub_process')->nullable();
            $table->string('lot_status')->nullable();
            $table->string('lot_id')->nullable();
            $table->string('model_id')->nullable();
            $table->integer('lot_qty')->nullable();
            $table->string('chip_size')->nullable();
            $table->string('work_type')->nullable();
            $table->string('hold_yn')->nullable();
            $table->float('tat_days')->nullable();
            $table->string('location')->nullable();
            $table->string('lot_details')->nullable();
            $table->string('routing_name')->nullable();
            $table->string('production_team_type')->nullable();
            $table->string('chip_type')->nullable();
            $table->string('special_code')->nullable();
            $table->string('powder_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vi_prod_wip_realtime');
    }
};