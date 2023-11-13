<?php

require_once(__ROOT__ . '/models/OmbrelloneModel.php');
require_once(__ROOT__ . '/models/PrenotazioniModel.php');
require_once(__ROOT__ . '/models/SpiaggiaModel.php');
require_once(__ROOT__ . '/models/EventoModel.php');
require_once(__ROOT__ . '/utils/UploadFiles.php');
require_once(__ROOT__ . '/views/DashboardView.php');

class DashboardController {

    public static function Dashboard () {
        // Incasso mensile e di stagione
        $conditions = array(
            "gestore" => $_SESSION["userId"]
        );
        $prenotazioni = PrenotazioniModel::where($conditions);

        $mesi = array();
        for($i = 0; $i < 12; $i++){
            $mesi[$i] = 0;
        }
        //Per ogni prenotazione trovata all'utente ne ricavo l'ombrellone
        // In base al mese e all'anno della prenotazione salvo il totale
        foreach ($prenotazioni as $prenotazione){
            $ombrellone = OmbrelloneModel::get($prenotazione->ombrellone);
            switch ($prenotazione->mese){
                case 1:
                    $mesi[0] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 2:
                    $mesi[1] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 3:
                    $mesi[2] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 4:
                    $mesi[3] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 5:
                    $mesi[4] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 6:
                    $mesi[5] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 7:
                    $mesi[6] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 8:
                    $mesi[7] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 9:
                    $mesi[8] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 10:
                    $mesi[9] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 11:
                    $mesi[10] =+ $ombrellone->prezzo_giornaliero;
                    break;
                case 12:
                    $mesi[11] =+ $ombrellone->prezzo_giornaliero;
                    break;
            }
        }

        $spiaggia = SpiaggiaModel::get($_GET["id"]);
        
        if( $spiaggia->user == $_SESSION["userId"]){
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

            $eventi = EventoModel::where(array(
                "spiaggia" => $spiaggia->id
            ));
        }
        //rendering template
        $spiagge = SpiaggiaModel::where(array("user"=>$_SESSION["userId"]));
        $view = new DashboardView();
        $view->render($mesi,$spiaggia,$eventi,$spiagge);
         
    }
    public static function crea_evento () {
        $data = array(
            "id" => null,
            "nome" => $_POST["nome"],
            "data" => $_POST["data"],
            "descrizione" => $_POST["descrizione"],
            "spiaggia" => $_POST["idSpiaggia"],
        );
        $evento = new EventoModel($data);

        try {
            $id = $evento->save();
            //creo una cartella per i file del task
            $spiaggia_folder = "spiagge_file/".$_POST["idSpiaggia"]."/eventi/".$id;
            if (!file_exists($spiaggia_folder)) {
            mkdir($spiaggia_folder, 0777, true);
            }
            $upload = new UploadFiles();
            $upload::upload("spiagge_file/".$_POST["idSpiaggia"]."/eventi/",$id);

            //header('Location: /dashboard');
        } catch (Exception $err) {
            $_SESSION["error"] = "Si è verificato un errore durante il salvataggio.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}