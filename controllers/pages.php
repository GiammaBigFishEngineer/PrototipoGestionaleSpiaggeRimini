<?php

require_once(__ROOT__ . '/models/OmbrelloneModel.php');
require_once(__ROOT__ . '/models/SpiaggiaModel.php');
require_once(__ROOT__ . '/views/PagesView.php');


class PagesController {

    public static function ricercaSpiagge () {
        $nome =  isset($_GET["nome"]) ? $_GET["nome"] : null;
        $citta =  isset($_GET["citta"]) ? $_GET["citta"] : null;
        
        if ($_GET){
            if(!empty($_GET['citta']) && empty($_GET['nome'])){
                $spiagge = SpiaggiaModel::where(array(
                    "citta" => $citta,
                ));
            }
            if(empty($_GET['citta']) && !empty($_GET['nome'])){
                $spiagge = SpiaggiaModel::where(array(
                    "nome" => $nome,
                ));
            }
            if(!empty($_GET['citta']) && !empty($_GET['nome'])){
                $spiagge = SpiaggiaModel::where(array(
                    "nome" => $nome,
                    "citta" => $citta,
                ));
            }
            if(empty($_GET['citta']) && empty($_GET['nome'])){
                $spiagge = SpiaggiaModel::all();
            }

        }else{
            $spiagge = SpiaggiaModel::all();
        }

        foreach ($spiagge as $spiaggia){

            $cartellaImmagini = "spiagge_file/".$spiaggia->id."/";
            $percorsiImmagini = array();
            // Scandisci la cartella e aggiungi i percorsi delle immagini all'array
            if ($handle = opendir($cartellaImmagini)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && $entry != ".DS_Store" && $entry != "eventi") {
                        $percorsiImmagini[] = $cartellaImmagini . $entry;
                    }
                }
                closedir($handle);
            }
            $spiaggia->immagini = $percorsiImmagini;
        }

        //Render View
        $view = new PagesView();
        $view->ricerca_spiaggia($spiagge);
    }

    public static function infoSpiaggia () {
        $id = isset($_GET["id"]) ? (int) $_GET["id"] : null;
        $spiaggia = SpiaggiaModel::get($id);

        $cartellaImmagini = "spiagge_file/".$spiaggia->id."/";
        $percorsiImmagini = array();

        // Scandisci la cartella e aggiungi i percorsi delle immagini all'array
        if ($handle = opendir($cartellaImmagini)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != ".DS_Store" && $entry != "eventi") {
                    $percorsiImmagini[] = $cartellaImmagini . $entry;
                }
            }
            closedir($handle);
        }
        
        $spiaggia->immagini = $percorsiImmagini;

        $indirizzo = $spiaggia->indirizzo;
        $citta = $spiaggia->citta;
        $provincia = $spiaggia->provincia;
        $spiaggia->coordinate = $spiaggia->CalcolaCordinate($indirizzo,$citta,$provincia);
        //Render View
        $view = new PagesView();
        $view->info_spiaggia($spiaggia);
    }
}