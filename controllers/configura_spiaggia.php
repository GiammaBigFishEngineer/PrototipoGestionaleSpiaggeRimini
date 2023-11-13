<?php
require_once(__ROOT__ . '/models/OmbrelloneModel.php');
require_once(__ROOT__ . '/models/PrenotazioniModel.php');
require_once(__ROOT__ . '/models/SpiaggiaModel.php');
require_once(__ROOT__ . '/views/SpiaggiaView.php');

class ConfSpiaggeController {

    public static function configura_spiaggia ()
    {
        $id =  isset($_GET["id"]) ? (int) $_GET["id"] : null;
        $spiaggia = SpiaggiaModel::get($id);
        $conditions = array(
            "spiaggia" => $spiaggia->id,
        );
        $matrice = array();
        if($spiaggia->user == $_SESSION['userId']){
            
            $ombrelloni = OmbrelloneModel::where($conditions);
            for($i = 0; $i < $spiaggia->n_righe; $i++){
                $matrice[$i] = array();
                for($k = 0; $k < $spiaggia->n_righe; $k++){
                    $matrice[$i][$k] = null;
                }
            }
            
            foreach($ombrelloni as $ombrellone){
                /* Inizializzo date per calendario prenotazioni*/
                $annoCorrente = date('Y');
                $mesiNomi = array(
                    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
                    7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                );
                $calendario = array();

                $riga = $ombrellone->riga;
                $colonna = $ombrellone->colonna;
                
                
                
                for ($mese = 1; $mese <= 12; $mese++) {
                    
                    $giorniDelMese = cal_days_in_month(CAL_GREGORIAN, $mese, $annoCorrente);
                    $meseNome = $mesiNomi[$mese];
                    
                    $calendario[$meseNome] = array();
                    
                    for ($giorno = 1; $giorno <= $giorniDelMese; $giorno++) {
                        // Controllo se esiste una prenotazione per questo giorno
                        $occupato = false;
                        $conditions = array(
                            "giorno" => $giorno,
                            "mese" => $mese,
                            "anno" => $annoCorrente,
                            "ombrellone" => $ombrellone->id
                        );
                        $occupati = PrenotazioniModel::where($conditions);
                    
                        $nonVuoti = array_filter($occupati, function($item) {
                            return !empty($item);
                        });
                    
                        if (!empty($nonVuoti)) {
                            // L'array $occupati contiene almeno un elemento non vuoto
                            $occupato = true;
                        }
                    
                        $calendario[$meseNome][] = array('giorno' => $giorno, 'prenotato' => $occupato);
                        
                        // Debug output
                        //echo "Mese: $meseNome, Giorno: $giorno, Occupato: " . ($occupato ? 'true' : 'false') . "<br>";
                    }
                }
                $ombrellone->calendario = $calendario;
                $matrice[$riga][$colonna] = $ombrellone;
            }
            
        }
        $annoCorrente = date('Y');
        //rendering template
        $spiagge = SpiaggiaModel::where(array("user"=>$_SESSION["userId"]));
        $view = new SpiaggiaView();
        $view->render($id,$matrice,$annoCorrente,$spiagge);
    }

    public static function aggiungi_riga ()
    {
        $id =  isset($_GET["id"]) ? (int) $_GET["id"] : null;
        $spiaggia = SpiaggiaModel::get($id);
        if($spiaggia->user == $_SESSION['userId']){
            $spiaggia->n_righe = $spiaggia->n_righe + 1;
            $prezzo_giorn_riga = $_POST["prezzo_giornaliero"];
            $prezzo_stag_riga = $_POST["prezzo_stagionale"];
            $id = $spiaggia->save();

            for ($i = 0; $i < $spiaggia->n_ombr_riga; $i++){

                $data = array(
                    "id" => null,
                    "numero" => $i,
                    "riga" => $spiaggia->n_righe,
                    "colonna" => $i,
                    "prezzo_giornaliero" => $prezzo_giorn_riga,
                    "prezzo_stagionale" => $prezzo_stag_riga,
                    "spiaggia" => $id
                );

                $ombrellone = new OmbrelloneModel($data);
                $ombrelloneId = $ombrellone->save();
            }
        }
        
        
        
        header('Location: /configura_spiaggia?id='.$id);
    }

    public static function riempi_riga () 
    {

        $id =  isset($_GET["id"]) ? (int) $_GET["id"] : null;
        $spiaggia = SpiaggiaModel::get($id);

        if($spiaggia->user == $_SESSION['userId']){
            
            $prezzo_giorn_riga = $_POST["prezzo_giornaliero"];
            $prezzo_stag_riga = $_POST["prezzo_stagionale"];
            

            for ($i = 0; $i < $spiaggia->n_ombr_riga; $i++){

                $data = array(
                    "id" => null,
                    "numero" => $i,
                    "riga" => $_POST["riga"],
                    "colonna" => $i,
                    "prezzo_giornaliero" => $prezzo_giorn_riga,
                    "prezzo_stagionale" => $prezzo_stag_riga,
                    "spiaggia" => $id
                );

                $ombrellone = new OmbrelloneModel($data);

                if (!$ombrellone->validate()) {
                    $_SESSION["error"] = implode(', ', $ombrellone->errors);
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit();
                }
                try {
                    $ombrelloneId = $ombrellone->save();
                    $_SESSION["message"] = "salvato con successo.";
        
                    //Test
                    header("Location: /configura_spiaggia?id=".$id);
                } catch (Exception $err) {
                    $_SESSION["error"] = "Si è verificato un errore durante il salvataggio del cliente.";
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                }
            }
        }
        
        
        
        header('Location: /configura_spiaggia?id='.$id);
    }

    public static function elimina_riga () 
    {

        $id =  isset($_GET["id"]) ? (int) $_GET["id"] : null;
        $spiaggia = SpiaggiaModel::get($id);

        if($spiaggia->user == $_SESSION['userId']){
           
            $conditions = array(
                "spiaggia" => $spiaggia->id,
                "riga" => $_GET["riga"]
            );
            $ombrelloni = OmbrelloneModel::where($conditions);
            foreach($ombrelloni as $ombrellone){
                OmbrelloneModel::delete($ombrellone->id);
            }
            
        }
        header('Location: /configura_spiaggia?id='.$id);
    }
    
    public static function aggiorna_ombrellone ()
    {
        
        $id =  isset($_GET["id"]) ? (int) $_GET["id"] : null;
        $spiaggiaId =  isset($_GET["spiaggia"]) ? (int) $_GET["spiaggia"] : null;
        $spiaggia = SpiaggiaModel::get($spiaggiaId);

        if($spiaggia->user == $_SESSION['userId']){
            $data = array(
                "id"=>$_POST['id'],
                "numero"=>$_POST['numero'],
                "riga"=>$_POST['riga'],
                "colonna"=>$_POST['colonna'],
                "prezzo_giornaliero"=>$_POST['prezzo_giornaliero'],
                "prezzo_stagionale"=>$_POST['prezzo_stagionale'],
                "spiaggia"=>$_POST['spiaggia'],
            );
            $ombrellone = new OmbrelloneModel($data);
        }

        if (!$ombrellone->validate()) {
            $_SESSION["error"] = implode(', ', $ombrellone->errors);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        try {
            $id = $ombrellone->save();
            header('Location: /configura_spiaggia?id='.$spiaggiaId);
        } catch (Exception $err) {
            $_SESSION["error"] = "Si è verificato un errore durante il salvataggio.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        
    }

    public static function prenota_ombrellone () 
    {

        $mesiNumeri = array(
            "Gennaio" => 1,
            "Febbraio" => 2,
            "Marzo" => 3,
            "Aprile" => 4,
            "Maggio" => 5,
            "Giugno" => 6,
            "Luglio" => 7,
            "Agosto" => 8,
            "Settembre" => 9,
            "Ottobre" => 10,
            "Novembre" => 11,
            "Dicembre" => 12
        );
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $giorno = $_POST["giorno"];
            $anno = $_POST["anno"];
            $ombrellone = $_POST["ombrellone"];
            $spiaggiaId = $_POST["spiaggiaId"];

            if (isset($_POST["mese"])) {
                $meseNome = $_POST["mese"];
                $meseNumero = $mesiNumeri[$meseNome];
            }
        }
        $spiaggia = SpiaggiaModel::get($spiaggiaId);

        $data = array(
            "id" => null,
            "giorno"=> $giorno,
            "mese" => $meseNumero,
            "anno" => $anno,
            "ombrellone" => $ombrellone,
            "cliente" => $_SESSION['userId'],
            "gestore" => $spiaggia->user,
        );
        $prenotazione = new PrenotazioniModel($data);

        if (!$prenotazione->validate()) {
            $_SESSION["error"] = implode(', ', $prenotazione->errors);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        try {
            $id = $prenotazione->save();
            header('Location: /settings');
        } catch (Exception $err) {
            $_SESSION["error"] = "Si è verificato un errore durante il salvataggio.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        
    }
}