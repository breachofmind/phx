<?php
use PHX\Wrapper as PHX;

require 'inc/helpers.php';
require 'vendor/autoload.php';

if (!function_exists('env')) {
    Dotenv::load(dirname(__FILE__));
}

//$phx = PHX::connect('accessifi_test','accessifi1','Sioux Falls');
//
//$customer = $phx->customer->getObject();
//
//echo $customer;