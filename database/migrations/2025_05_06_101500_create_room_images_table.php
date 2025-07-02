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
        Schema::create('room_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade'); // Tạo khóa ngoại
            $table->string('image_url');
            $table->boolean('is_active')->default(true); // Trường is_active
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_images');
    }
};
