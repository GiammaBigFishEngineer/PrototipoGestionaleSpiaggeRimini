<?php
require_once(__ROOT__ . '/vendor/autoload.php');
require_once(__ROOT__ . '/config/DB.php');


require_once('BaseModel.php');

class EventoModel extends BaseModel
{
    public static string $nome_tabella = 'Eventi';
    
    protected array $_fields = [
        "id",
        "nome",
        "data",
        "descrizione",
        "spiaggia"
    ];
   
    
}
