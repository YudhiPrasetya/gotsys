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
        Schema::table('process_sequence_orders', function (Blueprint $table) {
            $table->string('process_name')->nullable()->after('process_sequence_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('process_sequence_orders', function (Blueprint $table) {
            $table->dropColumn('process_name');
        });
    }
};
