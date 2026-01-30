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
        Schema::table('product_options', function (Blueprint $table) {

            if (Schema::hasColumn('product_options', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }

            if (Schema::hasColumn('product_options', 'option_group')) {
                $table->dropColumn('option_group');
            }

            $table->foreignId('product_option_group_id')
                ->after('id')
                ->nullable()
                ->constrained('product_option_groups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_options', function (Blueprint $table) {
            $table->foreignId('product_id')->after('id')->constrained('products')->onDelete('cascade');
            $table->string('option_group')->after('id');

            $table->dropForeign(['product_option_group_id']);
            $table->dropColumn('product_option_group_id');
        });
    }
};
