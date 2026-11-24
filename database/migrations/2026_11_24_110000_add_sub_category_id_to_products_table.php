<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Optional (I Have / I Want dono me). Sub category delete ho to product me null ho jayega.
            $table->foreignId('sub_category_id')->nullable()->after('category')
                ->constrained('sub_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sub_category_id');
        });
    }
};
