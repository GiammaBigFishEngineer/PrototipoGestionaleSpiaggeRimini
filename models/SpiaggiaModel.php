<?php
require_once(__ROOT__ . '/vendor/autoload.php');
require_once(__ROOT__ . '/config/DB.php');


require_once('BaseModel.php');

class SpiaggiaModel extends BaseModel
{
    public static string $nome_tabella = 'Spiaggia';
    
    protected array $_fields = [
        "id",
        "n_righe",
        "n_ombr_riga",
        "user",
        "nome",
        "descrizione",
        "indirizzo",
        "citta",
        "provincia",
    ];
   
    public function CalcolaCordinate($indirizzo,$citta,$provincia) 
    {
    //$indirizzo = "Via Roma, 1, Milano, Italia";
    $API_KEY = "AIzaSyBhjarX7Sv1a9w7hFlj8hN2b-IlBt7MGpo";
    $address = $indirizzo.' '.$citta.' '.$provincia;
    // Costruisce l'URL per la richiesta all'API di Geocoding di Google Maps
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address)."&key=".$API_KEY;
    
    // Effettua la richiesta HTTP all'API e decodifica la risposta JSON
    $response = file_get_contents($url);
    $json = json_decode($response);

    // Verifica se la richiesta ha restituito un risultato valido
    if ($json->status !== 'OK') {
        // La richiesta ha restituito un errore, gestiscilo di conseguenza
        var_dump($json);
        return false;
    }

    // Estrae le coordinate dal campo geometry.location dell'oggetto result
    $latitude = $json->results[0]->geometry->location->lat;
    $longitude = $json->results[0]->geometry->location->lng;
    // Restituisce le coordinate come un array associativo
    return [$latitude,$longitude];
    }
}
