<?php

require __DIR__ . '/session.php';

/*
|--------------------------------------------------------------------------
| SESSION CHECK (OPTIONAL UX IMPROVEMENT)
|--------------------------------------------------------------------------
*/
$isLoggedIn = isset($_SESSION['user_id']);

include __DIR__ . '/header.php';
include __DIR__ . '/navbar.php';

?>

<section class="hero">

<div>

<h1>Explore Paradise 🌴</h1>

<p>Book Amazing Fiji Tours Today</p>

<?php if (!$isLoggedIn): ?>

    <a href="/register.php"
       class="btn btn-warning btn-lg">
        Get Started
    </a>

<?php else: ?>

    <a href="/redirect.php"
       class="btn btn-success btn-lg">
        Go to Dashboard
    </a>

<?php endif; ?>

</div>

</section>

<div class="container mt-5">

<div class="row">

<div class="col-md-4">

<div class="card p-4 text-center">

<h3>🏝 Island Tours</h3>
<p>Discover Fiji's best islands.</p>

</div>

</div>

<div class="col-md-4">

<div class="card p-4 text-center">

<h3>✈ Adventure Trips</h3>
<p>Exciting travel experiences.</p>

</div>

</div>

<div class="col-md-4">

<div class="card p-4 text-center">

<h3>🏨 Luxury Resorts</h3>
<p>Premium accommodation packages.</p>

</div>

</div>

</div>

</div>

<?php include __DIR__ . '/footer.php'; ?>
