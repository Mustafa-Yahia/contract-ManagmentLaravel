<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('devices', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->unsignedBigInteger('category_id');
        $table->text('description');
        $table->decimal('price', 8, 2);
        $table->enum('availability_status', ['available', 'out_of_stock']);      
        $table->string('img')->nullable();
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
