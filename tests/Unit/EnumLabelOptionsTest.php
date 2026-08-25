<?php

use App\Enums\AdministratorRole;
use App\Enums\UserDocumentType;

test('enum labels are resolved from the shared trait', function () {
    expect(UserDocumentType::CE->label())->toBe('CE')
        ->and(AdministratorRole::SUPER_ADMIN->label())->toBe('Super Administrador');
});

test('enum options are generated from the shared trait', function () {
    expect(UserDocumentType::options())->toBe([
        ['value' => UserDocumentType::DNI->value, 'label' => 'DNI'],
        ['value' => UserDocumentType::CE->value, 'label' => 'CE'],
        ['value' => UserDocumentType::PASAPORTE->value, 'label' => 'Pasaporte'],
    ]);
});
