<?php

declare(strict_types=1);

use App\Controller\Controller;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/functions/functions.php';
require __DIR__ . '/bootstrap.php';


if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    return;
}

if ($_SERVER['REQUEST_URI'] === '/login') {
    $cont = new Controller($rememberMeService);

    $cont->login();
}

if ($_SERVER['REQUEST_URI'] === '/page') {
    $cont = new Controller($rememberMeService);

    $cont->page();
}

if ($_SERVER['REQUEST_URI'] === '/logout') {
    $cont = new Controller($rememberMeService);

    $cont->logout();
}

if ($_SERVER['REQUEST_URI'] === '/logoutAll') {
    $cont = new Controller($rememberMeService);

    $cont->logoutAll();
}

if ($_SERVER['REQUEST_URI'] === '/') {
    $cont = new Controller($rememberMeService);

    $cont->index();
}
