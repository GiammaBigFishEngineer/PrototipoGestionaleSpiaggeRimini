<?php

require_once(__ROOT__ . '/vendor/autoload.php');

/*
 * OGNI VIEW è ASSOCIATA AL RENDERING DI UNA PAGINA E AL RELATIVO TEMPLATE
 * L'USO DI TWIG PERMETTE DI PASSARE LE VARIABILI AL TEMPLATE IN MODO FACILE E VELOCE
 */

class PagesView
{

    public function ricerca_spiaggia($spiagge)
    {
        // Carica il template Twig
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        // Passa la lista dei clienti alla vista
        echo $twig->render('ricerca_spiagge.html.twig', [
            'spiagge' => $spiagge
        ]);
    }

    public function info_spiaggia($spiaggia)
    {
        // Carica il template Twig
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        
        // Passa la lista dei clienti alla vista
        echo $twig->render('info_spiaggia.html.twig', [
            'spiaggia' => $spiaggia
        ]);
    }
}
