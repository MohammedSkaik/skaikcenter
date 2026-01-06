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
        Schema::table('grade_subject', function (Blueprint $table) {
            $table->dropColumn('price_package'); // Moved to grades table
            $table->unique(['grade_id', 'subject_id']); // Prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_subject', function (Blueprint $table) {
            $table->decimal('price_package', 10, 2)->default(0);
            $table->dropUnique(['grade_id', 'subject_id']);
        });
    }
};
