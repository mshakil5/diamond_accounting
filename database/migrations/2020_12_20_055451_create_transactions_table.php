<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->string('table_type');
            $table->string('ref');
            $table->string('description')->nullable();
            $table->date('t_date');
            $table->decimal('amount',14,2)->nullable();
            $table->float('t_rate',10,2)->nullable();
            $table->decimal('t_amount',14,2)->nullable();
            $table->decimal('at_amount',14,2)->nullable();
            $table->string('transaction_type')->index();
            $table->string('payment_type')>nullable();
            $table->integer('employee_id')->nullable();
            $table->integer('asset_id')->nullable();
            $table->integer('liability_id')->nullable();
            $table->integer('expense_id')->nullable();
            $table->string('branch_id');
            $table->string('user_type')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
