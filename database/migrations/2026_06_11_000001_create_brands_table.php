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
        Schema::create('brands', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name', 128);
            $table->string('slug', 128)->unique();
            $table->char('code', 6)->unique();
            $table->string('short_description', 128)->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('code');
            $table->index('name');
            $table->index('slug');
            $table->index('is_active');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'pgsql', 'sqlsrv'], true)) {
            DB::statement("ALTER TABLE brands ADD CONSTRAINT chk_brands_code_format CHECK (code REGEXP '^BR[A-Z0-9]{4}$')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
