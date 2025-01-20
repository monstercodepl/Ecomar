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
        Schema::table('trucks', function (Blueprint $table) {
            $table->foreignId('job_1')->nullable();
            $table->foreignId('job_2')->nullable();
            $table->foreignId('job_3')->nullable();
            $table->foreignId('job_4')->nullable();
            $table->foreignId('job_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropColumn('job_1');
        $table->dropColumn('job_2');
        $table->dropColumn('job_3');
        $table->dropColumn('job_4');
        $table->dropColumn('job_5');
    }
};
