<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->string('booking_code', 32)->unique('idx_bookings_code');
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('slot_id')->constrained()->restrictOnDelete();
            $t->enum('status', ['pending','paid','confirmed','checked_in','cancelled'])->default('pending');
            $t->decimal('total_amount', 12, 2)->default(0);
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at'], 'idx_bookings_status_created');
            $t->index(['slot_id', 'status'], 'idx_bookings_slot_status');
            $t->index(['user_id', 'created_at'], 'idx_bookings_user_created');
            $t->index('paid_at', 'idx_bookings_paid_at');
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
