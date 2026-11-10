<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('id_number', 32)->nullable();
            $t->string('phone', 20)->nullable();
            $t->timestamps();
            $t->index('booking_id', 'idx_items_booking');
            $t->index('name', 'idx_items_name');
        });
    }
    public function down(): void { Schema::dropIfExists('booking_items'); }
};
