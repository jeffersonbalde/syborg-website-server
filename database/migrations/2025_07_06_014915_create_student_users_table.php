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
    Schema::create('tbl_StudentUser', function (Blueprint $table) {
        $table->id();
        $table->string('edp_number')->unique();
        $table->string('firstname');
        $table->string('middlename');
        $table->string('lastname');
        $table->string('course');
        $table->string('year_level');
        $table->string('status');
        $table->string('gender');
        $table->integer('age');
        $table->date('birthday');
        $table->string('contact_number');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('profile_picture')->nullable();
        $table->integer("active_status")->default(value: 2);
        $table->string('qr_code')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_StudentUser');
    }
};
