<?php

declare(strict_types=1);

test('renders the terms and conditions page', function () {
    $response = $this->get(route('terms'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('TermsPage'));
});
