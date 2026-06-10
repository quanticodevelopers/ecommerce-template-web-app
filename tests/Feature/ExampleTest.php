<?php

test('returns a successful response', function () {
    $response = $this->get(route('store.home'));

    $response->assertOk();
});
