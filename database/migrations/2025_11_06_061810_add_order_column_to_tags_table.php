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
        Schema::table('tags', function (Blueprint $table) {
            if (!Schema::hasColumn('tags', 'order_column')) {
                $table->integer('order_column')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            if (Schema::hasColumn('tags', 'order_column')) {
                $table->dropColumn('order_column');
            }
        });
    }
};
