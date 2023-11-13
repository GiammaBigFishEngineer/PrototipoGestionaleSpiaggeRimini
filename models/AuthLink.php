<?php

require_once('BaseModel.php');

class AuthLink extends BaseModel
{
    public static string $nome_tabella = 'AuthLink';
    protected array $_fields = [
        "id",
        "token",
        "user",
        "expiration",
    ];

}