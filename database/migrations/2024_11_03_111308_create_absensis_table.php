<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi dengan User
            $table->timestamps();

            $table->unique(['room_id', 'user_id']); // Agar seorang user hanya bisa absen sekali per room
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensis');
    }
};
