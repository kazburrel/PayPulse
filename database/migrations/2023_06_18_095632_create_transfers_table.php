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
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id')->nullable();
            $table->string('ini_tran_id')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('country')->nullable();
            $table->string('amount')->nullable();
            $table->string('beneficial_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('otp')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
