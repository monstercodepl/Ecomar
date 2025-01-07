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
            $table->string('vin')->nullable();
            $table->string('oc_number')->nullable();
            $table->dateTime('oc_date')->nullable();
            $table->dateTime('inspection_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn('vin');
            $table->dropColumn('oc_number');
            $table->dropColumn('oc_date');
            $table->dropColumn('inspection_date');
        });
    }
};
