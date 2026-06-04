<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Client;

$client = new Client();

// 🔑 Google credentials
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);

// 🔁 MUST match Google Console exactly
$client->setRedirectUri('http://localhost/IS35126Group22/auth/google-callback.php');

// 📌 Permissions
$client->addScope('email');
$client->addScope('profile');

// 🔐 recommended settings
$client->setAccessType('offline');
$client->setPrompt('select_account consent');