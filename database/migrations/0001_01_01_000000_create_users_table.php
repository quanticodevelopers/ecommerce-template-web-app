<?php

use App\Enums\AdministratorRole;
use App\Enums\UserDocumentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('document_type', 9);
            $table->string('document_number', 12);
            $table->string('name', 64);
            $table->string('last_name', 64);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->char('phone', 9);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['document_type', 'document_number'], 'customers_document_unique');
            $table->index('name');
            $table->index('last_name');
            $table->index('phone');
        });

        Schema::create('administrators', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name', 64);
            $table->string('last_name', 64);
            $table->string('email')->unique();
            $table->char('phone', 9);
            $table->string('password');
            $table->string('role', 11);
            $table->rememberToken();
            $table->timestamps();

            $table->index('name');
            $table->index('last_name');
            $table->index('phone');
        });

        $this->addCheckConstraints();

        Schema::create('customer_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('administrator_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id', 26)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('administrator_password_reset_tokens');
        Schema::dropIfExists('customer_password_reset_tokens');
        Schema::dropIfExists('administrators');
        Schema::dropIfExists('customers');
    }

    private function addCheckConstraints(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'pgsql', 'sqlsrv'], true)) {
            return;
        }

        $documentTypes = collect(UserDocumentType::cases())
            ->map(fn (UserDocumentType $type): string => "'{$type->value}'")
            ->implode(',');
        $administratorRoles = collect(AdministratorRole::cases())
            ->map(fn (AdministratorRole $role): string => "'{$role->value}'")
            ->implode(',');

        DB::statement("ALTER TABLE customers ADD CONSTRAINT chk_customers_document_type CHECK (document_type IN ({$documentTypes}))");
        DB::statement("ALTER TABLE administrators ADD CONSTRAINT chk_administrators_role CHECK (role IN ({$administratorRoles}))");
    }
};
