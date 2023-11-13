<?php
/*
Non funziona se non è già esistente la cartella, assicurati che $percorso sia già esistente
*/
class UploadFiles {

    public static function upload($percorso,$task_id) {
        $target_dir = $percorso . $task_id . "/";
        $uploadOk = 1;
    
        foreach($_FILES['file']['tmp_name'] as $key => $tmp_name) {
            $target_file = $target_dir . basename($_FILES['file']['name'][$key]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
            // Verifica se il file esiste già
            if (file_exists($target_file)) {
                echo "Il file " . $_FILES['file']['name'][$key] . " esiste già.";
                $uploadOk = 0;
            }
    
            // Verifica la dimensione del file
            if ($_FILES['file']['size'][$key] > 5000000) {
                echo "Il file " . $_FILES['file']['name'][$key] . " è troppo grande.";
                $uploadOk = 0;
            }
    
            // Verifica il tipo di file
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "exc" && $imageFileType != "zip" ) {
                echo "Sono consentiti solo i file JPG, JPEG, PNG e GIF, PDF, EXC, ZIP.";
                $uploadOk = 0;
            }
    
            // Verifica se è stato raggiunto il limite massimo di file
            if (count(glob($target_dir."*")) >= 10) {
                echo "Hai già caricato il massimo di 10 file.";
                $uploadOk = 0;
            }
            
            // Se non ci sono errori, carica il file
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES['file']['tmp_name'][$key], $target_file)) {
                    echo "Il file " . $_FILES['file']['name'][$key] . " è stato caricato.";
                } else {
                    echo "Si è verificato un errore durante il caricamento del file " . $_FILES['file']['name'][$key] . ".";
                }
            }
        }
    }
}