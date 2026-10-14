<?php

test('guest accessing home is redirected to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
