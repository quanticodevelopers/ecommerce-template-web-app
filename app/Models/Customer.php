<?php

namespace App\Models;

use App\Enums\UserDocumentType;
use Database\Factories\CustomerFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
 * @property Carbon|null $email_verified_at
 * @property string $phone
 * @property string $password
 * @property Carbon|null $created_at
 */
#[Fillable(['document_type', 'document_number', 'name', 'last_name', 'email', 'phone', 'password'])]
#[Hidden(['password', 'remember_token'])]
class Customer extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, HasUlids, Notifiable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'document_type' => UserDocumentType::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
