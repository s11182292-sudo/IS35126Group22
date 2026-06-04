<?php

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '', // leave blank for localhost
        'secure' => false, // set true only on HTTPS
        'httponly' => true,
        'samesite' => 'Lax' // IMPORTANT: must be Lax for OAuth (NOT Strict)
    ]);

    session_start();
}