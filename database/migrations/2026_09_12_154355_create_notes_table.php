<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->longText('note')->nullable(); // longText for Summernote HTML content
            $table->unsignedBigInteger('branch_id');
            $table->boolean('status')->default(1)->nullable(); // 1 = Active, 0 = Inactive
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); // For Soft Deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
}