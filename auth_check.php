<?php

require '../session.php';

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}
