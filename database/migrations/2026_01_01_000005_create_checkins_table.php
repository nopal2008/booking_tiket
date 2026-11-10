<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('checkins', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $t->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('checked_at');
            $t->timestamps();
            $t->index('booking_id', 'idx_checkins_booking');
            $t->index('checked_at', 'idx_checkins_checked_at');
        });
    }
    public function down(): void { Schema::dropIfExists('checkins'); }
};
