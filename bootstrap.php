<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

if (file_exists(".env")) {
    $dotenv = new DotEnv(__DIR__);
    $dotenv->load();
}