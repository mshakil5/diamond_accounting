<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegularsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regulars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('agt')->nullable();
            $table->string('ref')->nullable();
            $table->string('orderno')->nullable();
            $table->decimal('cash',14,2)->default(0);
            $table->decimal('bank',14,2)->default(0);
            $table->decimal('eviivo',14,2)->default(0);
            $table->decimal('parking_cash',14,2)->default(0);
            $table->decimal('parking_card',14,2)->default(0);
            $table->decimal('other_sales',14,2)->default(0);
            $table->decimal('returnamount',14,2)->default(0);
            $table->decimal('advance_sales',14,2)->default(0);
            $table->string('remark')->nullable();
            $table->date('date');

            $table->string('branch_id');
            $table->string('user_type');
            $table->string('updated_by');
            $table->string('created_by');

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
        Schema::dropIfExists('regulars');
    }
}
