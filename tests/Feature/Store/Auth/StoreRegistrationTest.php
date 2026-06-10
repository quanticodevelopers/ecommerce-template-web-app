<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('store.auth.register'));

    $response->assertOk();
});

test('new customer users can register', function () {
    $response = $this->post(route('register.store'), [
        'document_type' => 'dni',
        'document_number' => '87654321',
        'name' => 'Rodrigo',
        'last_name' => 'Quispe',
        'email' => 'rodrigo.admin@example.com',
        'phone' => '963852741',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('store.home', absolute: false));
});
