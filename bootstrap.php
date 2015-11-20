<?php
use PHX\Wrapper as PHX;

require 'inc/helpers.php';
require 'vendor/autoload.php';

if (!function_exists('env')) {
    Dotenv::load(dirname(__FILE__));
}