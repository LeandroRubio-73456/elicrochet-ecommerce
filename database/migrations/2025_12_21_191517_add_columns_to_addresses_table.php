<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('addresses', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'street')) {
                $table->string('street')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'province')) {
                $table->string('province')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
            if (!Schema::hasColumn('addresses', 'details')) {
                $table->text('details')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Safe drop
            $columnsToDrop = [];
            if (Schema::hasColumn('addresses', 'user_id')) $columnsToDrop[] = 'user_id';
            if (Schema::hasColumn('addresses', 'customer_name')) $columnsToDrop[] = 'customer_name';
            if (Schema::hasColumn('addresses', 'customer_email')) $columnsToDrop[] = 'customer_email';
            if (Schema::hasColumn('addresses', 'phone')) $columnsToDrop[] = 'phone';
            if (Schema::hasColumn('addresses', 'street')) $columnsToDrop[] = 'street';
            if (Schema::hasColumn('addresses', 'city')) $columnsToDrop[] = 'city';
            if (Schema::hasColumn('addresses', 'province')) $columnsToDrop[] = 'province';
            if (Schema::hasColumn('addresses', 'postal_code')) $columnsToDrop[] = 'postal_code';
            if (Schema::hasColumn('addresses', 'details')) $columnsToDrop[] = 'details';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
