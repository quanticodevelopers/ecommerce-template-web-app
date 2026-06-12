<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name', 128);
            $table->string('slug', 128)->unique();
            $table->char('code', 6)->unique();
            $table->ulid('parent_id')->nullable();
            $table->string('short_description', 128)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')->on('categories')
                ->nullOnDelete();

            $table->index('code');
            $table->index('name');
            $table->index('slug');
            $table->index('parent_id');
            $table->index('is_active');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'pgsql', 'sqlsrv'], true)) {
            DB::statement("ALTER TABLE categories ADD CONSTRAINT chk_categories_code_format CHECK (code REGEXP '^CA[A-Z0-9]{4}$')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
