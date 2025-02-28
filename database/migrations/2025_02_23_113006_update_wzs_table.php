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
            $table->text('client_name')->nullable();
            $table->text('client_address')->nullable();
            $table->decimal('price')->nullable();
            $table->decimal('amount')->nullable();
            $table->foreignId('userId')->nullable();
            $table->foreignId('addressId')->nullable();
            $table->boolean('sent')->default(true);
            $table->boolean('paid')->default(false);
            $table->foreignId('job_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wzs', function (Blueprint $table) {
            $table->dropColumn('client_name');
            $table->dropColumn('client_address');
            $table->dropColumn('price');
            $table->dropColumn('amount');
            $table->dropColumn('userId');
            $table->dropColumn('addressId');
            $table->dropColumn('sent');
            $table->dropColumn('paid');
            $table->foreignId('job_id')->change();
        });
    }
};
