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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            //id пользователя
            $table->unsignedBigInteger('user_id');
            //сумма транзакции
            $table->decimal('sum', 8, 2);
            //статус: зачисление, списание, перевод, получение баланса
            $table->enum('status', ['cashin', 'cashout', 'transfer_cashout', 'transfer_cashin', 'get_balance']);
            //id пользователя принимающего участие в транзакциях со статусом перевод 
            $table->unsignedBigInteger('user_id_transfer')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};