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
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Backfill: Assign existing grades to the first found academic year, or create one if missing
        $defaultYearId = \DB::table('academic_years')->value('id');

        if (!$defaultYearId) {
            $defaultYearId = \DB::table('academic_years')->insertGetId([
                'name' => '2024/2025',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        \DB::table('grades')->update(['academic_year_id' => $defaultYearId]);

        // Now make it required
        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_year_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};
