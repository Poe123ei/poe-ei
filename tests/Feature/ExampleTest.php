<?php

/**
 * Smoke test: the home page loads.
 *
 * Currently FAILS because routes/web.php has no "/" route. Fixing it is
 * Milestone 1, Task 1.2.
 */
test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
