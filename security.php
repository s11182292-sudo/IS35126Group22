<?php

function safe($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}