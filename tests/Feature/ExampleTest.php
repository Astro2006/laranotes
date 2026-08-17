<?php

test('the application redirects the root url to the notes index', function () {
    $response = $this->get('/');

    $response->assertRedirect('/notes');
});
