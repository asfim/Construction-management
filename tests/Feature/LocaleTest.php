<?php

test('guest or user can switch locale to english and back to bangla', function () {
    $response = $this->get(route('locale.switch', 'en'));
    $response->assertSessionHas('locale', 'en');

    $response2 = $this->get(route('locale.switch', 'bn'));
    $response2->assertSessionHas('locale', 'bn');
});
