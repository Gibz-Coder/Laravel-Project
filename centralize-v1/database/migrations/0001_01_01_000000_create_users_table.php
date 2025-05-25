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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->nullable(false); // bigint, pk, nn, un, ai
            $table->string('user_type')->nullable(false); // varchar(255), nn
            $table->string('user_stat')->nullable(false); // varchar(255), nn
            $table->string('nick_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('user_id')->nullable();
            $table->string('knox_id')->nullable();
            $table->string('knox_email')->nullable();
            $table->string('date_hired')->nullable(); // Added date_hired column
            $table->string('position')->nullable(); // Added position column
            $table->string('gender')->nullable();
            $table->string('bio')->nullable();
            $table->string('picture')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable(false); // Adding password column
            $table->string('rem_token')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('knox_email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
