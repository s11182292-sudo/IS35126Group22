<?php

session_start();

function generateCSRF()
{
    if(empty($_SESSION['csrf']))
    {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function verifyCSRF($token)
{
    return isset($_SESSION['csrf']) &&
           hash_equals($_SESSION['csrf'], $token);
}