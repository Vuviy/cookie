<?php

declare(strict_types=1);

use App\Controller\Controller;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions/functions.php';


if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    return;
}

if ($_SERVER['REQUEST_URI'] === '/login') {
    $cont = new Controller();

    $cont->login();
}

if ($_SERVER['REQUEST_URI'] === '/page') {
    $cont = new Controller();

    $cont->page();
}

if ($_SERVER['REQUEST_URI'] === '/logout') {
    $cont = new Controller();

    $cont->logout();
}

if ($_SERVER['REQUEST_URI'] === '/logoutAll') {
    $cont = new Controller();

    $cont->logoutAll();
}

if ($_SERVER['REQUEST_URI'] === '/') {
    $cont = new Controller();

    $cont->index();
}

//if ($_SERVER['REQUEST_URI'] === '/login') {
//    $cont = new \App\Controller\AuthController();
//
//    $cont->login();
//}
