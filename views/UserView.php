<?php

require_once(__ROOT__ . '/vendor/autoload.php');

class UserView
{
    public function renderLogin()
    {
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        
        echo $twig->render('static/login.html.twig', [
            'action' => '/login',
        ]);
    }

    public function renderSignup()
    {
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        
        echo $twig->render('static/signup.html.twig', [
            'action' => '/signup',
        ]);
    }
}