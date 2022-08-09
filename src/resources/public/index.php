<?php session_start();

// Entry points in the application

require_once '../vendor/autoload.php';

use Hummel\PhpFrame\Services\RouterSingleton;

RouterSingleton::getInstance()->routerAction($_SERVER['REQUEST_URI']);