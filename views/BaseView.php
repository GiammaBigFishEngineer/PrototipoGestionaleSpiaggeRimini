<?php

require_once(__ROOT__ . '/vendor/autoload.php');

/*
 * OGNI VIEW è ASSOCIATA AL RENDERING DI UNA PAGINA E AL RELATIVO TEMPLATE
 * L'USO DI TWIG PERMETTE DI PASSARE LE VARIABILI AL TEMPLATE IN MODO FACILE E VELOCE
 * 
 * Il seguente echo dentro ad ogni funzione di classe passa i parametri al tempalte
 * echo $this->twig->render('optometria/list.html.twig', [
            'variabile' => $variabile
        ]);
 */

class BaseView
{
    public $twig;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $this->twig = new \Twig\Environment($loader);

        if (isset($_SESSION)) $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addGlobal('get', $_GET);
        $this->twig->addGlobal('post', $_POST);
    }

    public function __destruct()
    {
        // clear the consumed sessions
        if (session_status() === PHP_SESSION_ACTIVE)
            $_SESSION = array();
    }
}
