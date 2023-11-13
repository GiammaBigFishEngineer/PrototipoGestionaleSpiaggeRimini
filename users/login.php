<?php
require_once(__ROOT__ . '/models/UserModel.php');
require_once(__ROOT__ . '/views/UserView.php');

class Login {

    public function send () {
        
        // Verifica se è stata inviata una richiesta POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recupera i valori dell'utente dalla richiesta
        $email = $_POST["email"];
        $password = $_POST["password"];
        UserModel::login($password,$email);
        
    } else {
        $view = new UserView();
        $view->renderLogin();
        } 
}


}