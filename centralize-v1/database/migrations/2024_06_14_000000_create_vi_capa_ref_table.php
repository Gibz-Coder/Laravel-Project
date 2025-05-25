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
        Schema::create('vi_capa_ref', function (Blueprint $table) {
            $table->id();
            $table->string('mc_no')->nullable();
            $table->string('mc_type')->nullable();
            $table->string('line')->nullable();
            $table->string('area')->nullable();
            $table->integer('daily_capa')->default(0);
            $table->integer('actual_capa')->default(0);
            $table->string('mc_size')->nullable();
            $table->string('mc_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vi_capa_ref');
    }
};