<?php
require_once(__ROOT__ . '/vendor/autoload.php');
require_once(__ROOT__ . '/config/DB.php');


require_once('BaseModel.php');

class PrenotazioniModel extends BaseModel
{
    public static string $nome_tabella = 'Prenotazioni';
    
    protected array $_fields = [
        "id",
        "giorno",
        "mese",
        "anno",
        "ombrellone",
        "cliente",
        "gestore"
    ];
   
    
}
