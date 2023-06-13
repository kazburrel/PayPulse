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
            $table->uuid('id')->primary();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('other_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('type')->nullable();
            $table->string('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();
            $table->string('country')->nullable();
            $table->string('id_card')->nullable();
            $table->string('selfie')->nullable();
            $table->string('account_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('t_c')->nullable();
            $table->string('security_question')->nullable();
            $table->string('username')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
