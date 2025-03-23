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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->text('brief');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->string('location')->nullable();
            $table->integer('views_number')->default(0)->nullable();
            $table->float('rating')->default(0)->nullable();
            $table->text('video')->nullable();
            $table->float('price')->nullable();
            $table->date('service_date')->nullable();
            $table->time('service_time_from')->nullable();
            $table->time('service_time_to')->nullable();
            $table->string('gender')->nullable();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
