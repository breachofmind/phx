<?php
require 'inc/helpers.php';
require 'vendor/autoload.php';

if (!function_exists('env')) {
    Dotenv::load(dirname(__FILE__));
}
