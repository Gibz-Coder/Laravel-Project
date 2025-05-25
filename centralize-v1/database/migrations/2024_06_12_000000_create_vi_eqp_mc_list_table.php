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
        Schema::create('vi_eqp_mc_list', function (Blueprint $table) {
            $table->id() // This automatically makes the column a primary key, non-null, and auto-incrementing
                  ->unsigned(); // Optional: if you want to make the id unsigned

            $table->string('mc_no')->nullable();
            $table->string('line')->nullable();
            $table->string('area')->nullable();
            $table->string('maker')->nullable();
            $table->string('mc_type')->nullable();
            $table->string('inspection_type')->nullable();
            $table->string('inspection_class')->nullable();
            $table->string('inspection_camera')->nullable();
            $table->string('feeder_type')->nullable();
            $table->string('IP_address')->nullable();

            // Integer columns with nullable default values
            $table->integer('nor_capa_02')->default(0);
            $table->integer('nor_capa_03')->default(0);
            $table->integer('nor_capa_05')->default(0);
            $table->integer('nor_capa_10')->default(0);
            $table->integer('nor_capa_21')->default(0);
            $table->integer('nor_capa_31')->default(0);
            $table->integer('nor_capa_32')->default(0);
            $table->integer('pr_capa_02')->default(0);
            $table->integer('pr_capa_03')->default(0);
            $table->integer('pr_capa_05')->default(0);
            $table->integer('pr_capa_10')->default(0);
            $table->integer('pr_capa_21')->default(0);
            $table->integer('pr_capa_31')->default(0);
            $table->integer('pr_capa_32')->default(0);

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vi_eqp_mc_list');
    }
};