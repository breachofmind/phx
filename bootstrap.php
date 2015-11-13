<?php
require 'inc/helpers.php';
require 'vendor/autoload.php';

if (!function_exists('env')) {
    Dotenv::load(dirname(__FILE__));
}

//$phx = new \PHX\Wrapper();
//$phx->testing();
//$phx->system->login();
