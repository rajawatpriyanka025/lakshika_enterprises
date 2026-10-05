<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('focus_keyword', 100)->nullable()->after('meta_description');
            $table->string('sku', 60)->nullable()->after('description');
            $table->string('material', 120)->nullable()->after('sku');
            $table->string('colour', 80)->nullable()->after('material');
            $table->string('dimensions', 120)->nullable()->after('colour');
            $table->string('weight', 60)->nullable()->after('dimensions');
            $table->decimal('price', 10, 2)->nullable()->after('weight');
            $table->string('availability', 20)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['focus_keyword', 'sku', 'material', 'colour', 'dimensions', 'weight', 'price', 'availability']);
        });
    }
};
