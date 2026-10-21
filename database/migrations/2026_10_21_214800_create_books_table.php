<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150);
            $table->string('author', 100);
            $table->string('isbn', 20)->unique();
            $table->string('publisher', 100);
            $table->unsignedSmallInteger('published_year');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->enum('status', ['available', 'out_of_stock', 'discontinued'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};