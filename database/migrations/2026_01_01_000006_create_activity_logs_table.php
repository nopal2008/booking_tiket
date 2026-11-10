<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 64);
            $t->string('subject_type')->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'created_at'], 'idx_logs_user_created');
            $t->index(['action', 'created_at'], 'idx_logs_action_created');
        });
    }
    public function down(): void { Schema::dropIfExists('activity_logs'); }
};
