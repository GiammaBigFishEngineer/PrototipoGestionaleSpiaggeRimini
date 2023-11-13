<?php

class ForgotPassword {
    public static function send () {
        //Invio email con link generato per autenticazione
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
            send_auth_pass($_POST['email']);
            echo "Email di autenticazione inviata a " . $_POST['email'];
        }
    }
    public static function change () {
        // Verifica se l'utente ha visitato il link di autenticazione
        if (isset($_POST['token']) && isset($_POST['expires'])) {
            $token = $_POST['token'];
            $expiration_date = $_POST['expires'];
            $expiration_time = strtotime($expiration_date);
            
            if ($token == $_SESSION['auth_token'] && $expiration_time == $_SESSION['auth_expires']) {
                //Seleziono utente tramite token
                $qr = "SELECT user FROM AuthLink WHERE token = :token LIMIT 1";
                $sth = DB::get()->prepare($qr);
                $sth->execute([
                    'token' => $token
                ]);
                $obj = $sth->fetch();
                $user = UserModel::get($obj['user']);

                $password = $_POST["password1"];
                $password2 = $_POST["password2"];
                if($password == $password2){
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $user->password = $password_hash;
                    $user->save();
                    echo "Password cambiata con successo";
                }else{
                    echo "Le due password non coincidono";
                }
                

                $query = "DELETE FROM AuthLink WHERE token = :token";
                $sth = DB::get()->prepare($query);
                $sth->execute([
                    'token' => $token,
                ]);
                // Cancella il token e la data di scadenza dal database o dalla sessione
                unset($_SESSION['auth_token']);
                unset($_SESSION['auth_expires']);
            }
        }
    }
}