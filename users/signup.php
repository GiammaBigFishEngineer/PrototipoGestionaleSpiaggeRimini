<?php

require_once(__ROOT__ . '/models/AuthLink.php');
require_once(__ROOT__ . '/utils/SendAuthLink.php');


class Signup {

    public function send () {

        // Verifica se è stata inviata una richiesta POST

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if(!$_POST['email'] || !$_POST['fullname'] || !$_POST['password'] || !$_POST['password2'] ){
           $message = "Tutti i campi sono obbligatori";
           header("Location: /signup?message=".$message);
        }
        
        // Recupera i valori dell'utente dalla richiesta
        $fullname = $_POST["fullname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $password2 = $_POST["password2"];
        if($password == $password2){
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $error = UserModel::signup_control($email);
            $data = array(
                "id"=>null,
                "fullname" => $fullname,
                "email" => $email,
                "password" => $password_hash,
                "loggedIn" => 0,
                "validated" => 0,
            );
            if($error == 0){
                $user = new UserModel($data);
                $id = $user->save();
                $data = array(
                    "id"=>$id,
                    "fullname" => $fullname,
                    "email" => $email,
                    "password" => $password_hash,
                    "loggedIn" => 0,
                    "validated" => 0,
                );
                $user = new UserModel($data);
                $id = $user->save();

                //Invio email con link generato per autenticazione
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
                    send_auth_link($id,$_POST['email']);
                    echo "Email di autenticazione inviata a " . $_POST['email'];
                }

                //header('Location: ' . $GLOBALS['url_frontend'].'login');
            }
            
        }else{
            $message = "le due password non coincidono";
            echo $message;
            //header('Location: /signup?message='.$message);
        }
        
    } else {
        $view = new UserView();
        $view->renderSignup();
        } 
}

}