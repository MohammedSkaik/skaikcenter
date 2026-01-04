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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('identification_number')->nullable()->unique()->after('phone');
            $table->date('dob')->nullable()->after('identification_number');
            $table->enum('gender', ['male', 'female'])->nullable()->after('dob');
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->after('gender');
            $table->string('type')->default('student')->after('status'); // admin, center_admin, teacher, student, guardian, accountant

            // Auditing & Soft Deletes
            $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
            $table->boolean('is_deleted')->default(false)->after('updated_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('is_deleted');
            $table->softDeletes()->after('deleted_by');

            // Foreign keys for auditing (optional, but good for integrity if users table is self-referencing)
            // Note: We might need to disable foreign key checks if users are deleted? 
            // For now, let's keep it simple without hard foreign keys for logs to allow log retention even if user is somehow force deleted (though we enforce soft delete).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'identification_number',
                'dob',
                'gender',
                'status',
                'type',
                'created_by',
                'updated_by',
                'is_deleted',
                'deleted_by',
                'deleted_at'
            ]);
        });
    }
};
