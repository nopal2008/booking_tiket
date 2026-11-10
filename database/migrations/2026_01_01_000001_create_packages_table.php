<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->decimal('price', 12, 2);
            $t->unsignedSmallInteger('capacity_per_slot')->default(10);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['is_active', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('packages'); }
};
