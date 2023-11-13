<?php

require_once(__ROOT__ . '/models/OmbrelloneModel.php');
require_once(__ROOT__ . '/models/SpiaggiaModel.php');
require_once(__ROOT__ . '/views/SettingsView.php');
require_once(__ROOT__ . '/utils/DeleteDirectory.php');

class SettingsController {

    public static function crea_spiaggia() 
    {

        if($_SESSION['userId']){
            $id = isset($_POST["id"]) ? (int) $_POST["id"] : null;
            $data = array(
                "id"=> $id,
                "n_righe"=>$_POST['righe'],
                "n_ombr_riga"=>$_POST['colonne'],
                "user"=>$_SESSION['userId'],
                "nome" => $_POST["nome"],
                "descrizione" => $_POST["descrizione"],
                "indirizzo" => $_POST["indirizzo"],
                "citta" => $_POST["citta"],
                "provincia" => $_POST["provincia"]
            );
        $spiaggia = new SpiaggiaModel($data);
        
        if (!$spiaggia->validate()) {
            $_SESSION["error"] = implode(', ', $spiaggia->errors);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        try {
            $id = $spiaggia->save();
            //creo una cartella per i file del task
            $spiaggia_folder = "spiagge_file/".$id;
            if (!file_exists($spiaggia_folder)) {
            mkdir($spiaggia_folder, 0777, true);
            }
            header('Location: /configura_spiaggia?id='.$id);
        } catch (Exception $err) {
            $_SESSION["error"] = "Si è verificato un errore durante il salvataggio.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        }
    }

    public static function lista_spiagge () 
    {
        $spiagge = null;
        if($_SESSION["userId"]){
            $conditions = array(
                "user" => $_SESSION["userId"]
            );
            $spiagge = SpiaggiaModel::where($conditions);
        }
        foreach($spiagge as $spiaggia){
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
        //rendering template
        $view = new SettingsView();
        $view->render($spiagge);
    }

    public static function elimina_spiaggia () 
    {
        $id = (int) $_GET["id"];
        $spiaggia = SpiaggiaModel::get($id);
        if($spiaggia->user == $_SESSION['userId']){
            
            try {
                SpiaggiaModel::delete($id);
                $dir = "spiagge_file/" . $id . "/";
                if (deleteDirectory($dir)) {
                    echo "Directory removed successfully.";
                } else {
                    echo "Error removing directory.";
                }
                $_SESSION["message"] = "Eliminazione effettuata con successo.";
                //header('Location: /settings');
            } catch (Exception $err) {
                $_SESSION["error"] = "Si è verificato un errore";
            }
        }else{
            echo "Non hai i permessi per eliminare questa spiaggia";
        }
    }
}