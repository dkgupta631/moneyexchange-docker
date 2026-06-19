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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->boolean('status')->default(true);
            $table->string('icon')->nullable();
            $table->timestamps();
        });
        DB::table('languages')->insert([
            ['name' => 'English', 'code' => 'en', 'order' => '0', 'status' => '1', 'icon' => 'http://127.0.0.1:8000/website/assets/flags/en.png'],
            ['name' => 'Thai', 'code' => 'th-TH', 'order' => '1', 'status' => '1', 'icon' => 'http://127.0.0.1:8000/website/assets/flags/th.png'],
            ['name' => 'Khmer', 'code' => 'km', 'order' => '2', 'status' => '1', 'icon' => 'http://127.0.0.1:8000/website/assets/flags/kh.png'],
           
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
