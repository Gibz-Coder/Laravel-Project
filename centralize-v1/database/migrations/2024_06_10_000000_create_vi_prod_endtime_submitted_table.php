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
        Schema::create('vi_prod_endtime_submitted', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->nullable(false); // pk, nn, ai
            $table->string('lot_id')->nullable();
            $table->string('model_id')->nullable();
            $table->integer('lot_qty')->nullable();
            $table->string('qty_class')->nullable();
            $table->string('chip_size')->nullable();
            $table->string('work_type')->nullable();
            $table->string('lot_type')->nullable();
            $table->string('mc_no')->nullable();
            $table->string('line')->nullable();
            $table->string('area')->nullable();
            $table->string('mc_type')->nullable();
            $table->string('inspection_type')->nullable();
            $table->string('lipas_yn')->nullable();
            $table->string('ham_yn')->nullable();
            $table->string('status')->nullable();
            $table->integer('week_no')->nullable();
            $table->date('endtime_date')->nullable(); // yyyy-mm-dd format
            $table->string('cutoff_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vi_prod_endtime_submitted');
    }
};