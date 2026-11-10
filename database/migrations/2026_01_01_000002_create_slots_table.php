<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('slots', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->unsignedSmallInteger('capacity');
            $t->unsignedSmallInteger('used')->default(0);
            $t->timestamps();
            $t->unique(['package_id', 'date'], 'idx_slots_package_date');
            $t->index(['date', 'capacity'], 'idx_slots_date_capacity');
        });
    }
    public function down(): void { Schema::dropIfExists('slots'); }
};
