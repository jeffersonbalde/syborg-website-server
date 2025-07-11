<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_Attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('tbl_Events')->onDelete('cascade');
            $table->foreignId('edp_number')->constrained('tbl_StudentUser')->onDelete('cascade');
            $table->timestamp('time_in')->nullable();
            $table->timestamp('time_out')->nullable();
            $table->boolean('present')->default(false);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_Attendance');
    }
};
