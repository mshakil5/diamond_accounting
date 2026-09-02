<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('address_first_line')->nullable();
            $table->string('address_second_line')->nullable();
            $table->string('address_third_line')->nullable();
            $table->string('town')->nullable();
            $table->string('postcode')->nullable();
            
            // Removed ->after() methods here:
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('allowed_radius')->default(300);
            
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};