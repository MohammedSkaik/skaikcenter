<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up Subjects Table (Make it global)
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn(['grade_id', 'price_package', 'price_single']);
        });

        // 2. Create Pivot Table
        Schema::create('grade_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');

            // Per-Grade Pricing
            $table->decimal('price_package', 10, 2)->default(0);
            $table->decimal('price_single', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_subject');

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->constrained('grades')->onDelete('cascade');
            $table->decimal('price_package', 10, 2)->default(0);
            $table->decimal('price_single', 10, 2)->default(0);
        });
    }
};
