<?php

require_once(__ROOT__ . '/vendor/autoload.php');

/*
 * OGNI VIEW è ASSOCIATA AL RENDERING DI UNA PAGINA E AL RELATIVO TEMPLATE
 * L'USO DI TWIG PERMETTE DI PASSARE LE VARIABILI AL TEMPLATE IN MODO FACILE E VELOCE
 */

class SettingsView
{

    public function render($lista)
    {
        // Carica il template Twig
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        // Passa la lista dei clienti alla vista
        echo $twig->render('settings.html.twig', [
            'spiagge' => $lista,
        ]);
    }

    
}
