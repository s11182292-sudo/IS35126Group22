<?php
date_default_timezone_set('Pacific/Fiji');
require '../config/session.php';
require '../config/google-config.php';

if (!isset($client)) {
    die("Google Client not configured.");
}

// Generate Google login URL
$authUrl = $client->createAuthUrl();

// Redirect user to Google
header("Location: " . $authUrl);
exit;