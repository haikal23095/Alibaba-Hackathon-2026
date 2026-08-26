<?php

test('dashboard requires authentication', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
