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
        Schema::table('wzs', function (Blueprint $table) {
            $table->integer('number');
            $table->foreignId('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wzs', function (Blueprint $table) {
            $table->dropColumn('number');
            $table->dropColumn('job_id');
        });
    }
};
