<?php

require_once __DIR__ . '/config/session.php';

/*
|--------------------------------------------------------------------------
| SESSION VALIDATION
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    session_unset();
    session_destroy();
    header("Location: auth/login.php");
    exit();
}

$role = $_SESSION['role'];

/*
|--------------------------------------------------------------------------
| ROLE-BASED REDIRECT (RBAC)
|--------------------------------------------------------------------------
*/
if ($role === 'admin') {

    header("Location: admin_dashboard.php");
    exit();

} elseif ($role === 'agent') {

    header("Location: agent_dashboard.php");
    exit();

} elseif ($role === 'customer') {

    header("Location: cus_dashboard.php");
    exit();

} else {

    /*
    |--------------------------------------------------------------------------
    | INVALID ROLE → FORCE LOGOUT (SECURITY HARDENING)
    |--------------------------------------------------------------------------
    */
    session_unset();
    session_destroy();

    header("Location: auth/login.php");
    exit();
}
