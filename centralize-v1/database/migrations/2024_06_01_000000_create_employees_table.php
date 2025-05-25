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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->nullable(false); // pk, nn, ai, un
            $table->string('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('date_hired')->nullable();
            $table->string('employee_knox')->nullable();
            $table->string('employee_process')->nullable();
            $table->string('employee_dept')->nullable();
            $table->string('position')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};