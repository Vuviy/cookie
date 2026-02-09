<?php

namespace App\Controller;

use App\Database\Database;
use App\RememberMeService;
use App\Repository\CoockieRepository;


final class Controller
{
    public function index()
    {
        $view = require  __DIR__ . '/../../view/home.php';
        return $view;
    }
    public function login()
    {
        $repository = new CoockieRepository(new Database(config()));
        $rememberMeService = new RememberMeService($repository);

        $rememberMeChecked = true;
        $userId = 445566;

        if ($rememberMeChecked) {
            $rememberMeService->createToken($userId);
        }

        echo 'login';
    }


    public function page()
    {
        $repository = new CoockieRepository(new Database(config()));
        $rememberMeService = new RememberMeService($repository);


        $rememberMeService->tryAutoLogin();


        echo 'page';
    }

    public function logout()
    {
        $repository = new CoockieRepository(new Database(config()));
        $rememberMeService = new RememberMeService($repository);

        $rememberMeService->logout();

        echo 'logout';
    }

    public function logoutAll()
    {
        $repository = new CoockieRepository(new Database(config()));
        $rememberMeService = new RememberMeService($repository);

        $rememberMeService->logoutAll(445566);

        echo 'logoutAll';
    }
}
