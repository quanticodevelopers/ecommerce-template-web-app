<?php

namespace App\Models;

use App\Enums\AdministratorRole;
use App\Enums\UserDocumentType;
use Database\Factories\AdministratorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property UserDocumentType $document_type
 * @property string $document_number
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property AdministratorRole $role
 * @property Carbon|null $created_at
 */
#[Fillable(['document_type', 'document_number', 'name', 'last_name', 'email', 'phone', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class Administrator extends Authenticatable
{
    /** @use HasFactory<AdministratorFactory> */
    use HasFactory, HasUlids, Notifiable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'document_type' => UserDocumentType::class,
            'password' => 'hashed',
            'role' => AdministratorRole::class,
        ];
    }
}
