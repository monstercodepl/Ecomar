<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('pk_documents', function (Blueprint $table) {
            $table->id();
            // Format numeru dokumentu: letter + number/month/year
            $table->string('letter')->default('P');
            $table->integer('number');
            $table->string('month', 2);
            $table->string('year', 4);
            // Dane klienta:
            $table->unsignedBigInteger('user_id');
            $table->string('client_name');
            // Kwota korekty – wartość, która zostanie dodana (lub odjęta) do salda
            $table->decimal('adjustment_value', 10, 2);
            // Opcjonalny komentarz
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('pk_documents');
    }
}
