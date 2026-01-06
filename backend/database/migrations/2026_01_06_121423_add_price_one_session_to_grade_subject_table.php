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
            $table->decimal('price_one_session', 10, 2)->default(0)->after('price_single');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_subject', function (Blueprint $table) {
            $table->dropColumn('price_one_session');
        });
    }
};
