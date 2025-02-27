<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingFieldsToWzsTable extends Migration
{
    public function up()
    {
        Schema::table('wzs', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('billing_status')->default('pending'); // pending, paid, overdue
            $table->string('payment_method')->nullable();
            $table->date('issued_at')->nullable();
            $table->string('document_type')->default('wz'); // 'wz', 'invoice' lub 'pk'
            $table->timestamp('paid_at')->nullable();
            $table->decimal('previous_year_balance', 10, 2)->nullable(); // tylko dla dokumentów PK
        });
    }

    public function down()
    {
        Schema::table('wzs', function (Blueprint $table) {
            $table->dropColumn([
                'paid_amount', 'billing_status', 'payment_method', 
                'issued_at', 'document_type', 'paid_at', 'previous_year_balance'
            ]);
        });
    }
}
