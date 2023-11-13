<?php

require_once(__ROOT__ . '/vendor/autoload.php');

/*
 * OGNI VIEW è ASSOCIATA AL RENDERING DI UNA PAGINA E AL RELATIVO TEMPLATE
 * L'USO DI TWIG PERMETTE DI PASSARE LE VARIABILI AL TEMPLATE IN MODO FACILE E VELOCE
 */

class SpiaggiaView
{

    public function render($spiaggiaId,$matrice,$anno,$spiagge)
    {
        // Carica il template Twig
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);
        // Passa la lista dei clienti alla vista
        echo $twig->render('configura_spiaggia.html.twig', [
            'matrice' => $matrice,
            'spiaggiaId' => $spiaggiaId,
            "anno" => $anno,
            'spiagge' => $spiagge
        ]);
    }

    
}
