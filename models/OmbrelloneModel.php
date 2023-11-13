<?php
require_once(__ROOT__ . '/vendor/autoload.php');
require_once(__ROOT__ . '/config/DB.php');


require_once('BaseModel.php');

class OmbrelloneModel extends BaseModel
{
    public static string $nome_tabella = 'Ombrelloni';
    
    protected array $_fields = [
        "id",
        "numero",
        "riga",
        "colonna",
        "prezzo_giornaliero",
        "prezzo_stagionale",
        "spiaggia"
    ];
   
    
}
