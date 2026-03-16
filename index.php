<?php
/**
 * Root index.php — Bootstrap for shared hosting compatibility.
 * Routes all requests through public/index.php
 * Works even when called via ErrorDocument 403/404
 */

// Reset status code to 200 (we're handling the route, not an error)
http_response_code(200);

// Change working directory to public/
chdir(__DIR__ . '/public');

// Include the main application entry point
require __DIR__ . '/public/index.php';
