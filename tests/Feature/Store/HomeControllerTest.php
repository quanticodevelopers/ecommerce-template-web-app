<?php

use Inertia\Testing\AssertableInertia as Assert;

test('store home page is displayed', function () {
    $this->get(route('store.home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('store/home/index'),
        );
});
