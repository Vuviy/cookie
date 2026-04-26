<?php
declare(strict_types=1);

namespace App\Controller;

use App\RememberMeService;

final class Controller
{

    public function __construct(private RememberMeService $rememberMeService)
    {
    }

    public function index()
    {
        $view = require  __DIR__ . '/../../view/home.php';
        return $view;
    }
    public function login()
    {
        // TODO: real check credentials
        // $userId = $this->userRepository->findByCredentials($login, $password)?->id;

        $rememberMeChecked = array_key_exists('remember_me', $_POST);
        $userId = 445566; // change in real


        if ($rememberMeChecked) {
            $this->rememberMeService->createToken($userId);
        }

        echo 'login';
    }


    public function page()
    {
        $this->rememberMeService->tryAutoLogin();
        echo 'page';
    }

    public function logout()
    {
        $this->rememberMeService->logout();

        echo 'logout';
    }

    public function logoutAll()
    {
        // $userId = $this->session->get('user_id'); // in real
        $this->rememberMeService->logoutAll(445566); // change

        echo 'logoutAll';
    }
}
