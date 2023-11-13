<?php

require_once(__ROOT__ . '/vendor/autoload.php');

/*
 * OGNI VIEW è ASSOCIATA AL RENDERING DI UNA PAGINA E AL RELATIVO TEMPLATE
 * L'USO DI TWIG PERMETTE DI PASSARE LE VARIABILI AL TEMPLATE IN MODO FACILE E VELOCE
 */

class DashboardView
{

    public function render($mesi,$spiaggia,$eventi,$spiagge)
    {
        // Carica il template Twig
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        // Passa la lista dei clienti alla vista
        echo $twig->render('dashboard.html.twig', [
            'mesi' => $mesi,
            'spiaggia' => $spiaggia,
            'eventi' => $eventi,
            'spiagge' => $spiagge
        ]);
    }

    
}
