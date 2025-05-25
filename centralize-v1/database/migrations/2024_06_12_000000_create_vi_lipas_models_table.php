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
        Schema::create('vi_lipas_models', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('model_id')->nullable();
            $table->string('lipas_yn')->nullable();
            $table->string('ham_yn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vi_lipas_models');
    }
};
