<?php

use App\Enums\UserDocumentType;
use App\Enums\UserRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('document_type', 9);
            $table->string('document_number', 12);
            $table->string('name', 64);
            $table->string('last_name', 64);

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->char('phone', 9);
            $table->string('password');
            $table->string('role', 11);

            $table->rememberToken();

            $table->timestamps();

            $table->unique(['document_type', 'document_number'], 'users_document_unique');

            $table->index(['document_type', 'document_number']);
            $table->index('name');
            $table->index('last_name');
            $table->index('email');
            $table->index('phone');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'pgsql', 'sqlsrv'], true)) {
            DB::statement(sprintf('ALTER TABLE users ADD CONSTRAINT chk_document_type CHECK (document_type IN (%s))',
                collect(UserDocumentType::cases())
                    ->map(fn ($t) => "'{$t->value}'")
                    ->implode(',')
            ));

            DB::statement(sprintf('ALTER TABLE users ADD CONSTRAINT chk_role CHECK (role IN (%s))',
                collect(UserRole::cases())
                    ->map(fn ($t) => "'{$t->value}'")
                    ->implode(',')
            ));
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUlid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
