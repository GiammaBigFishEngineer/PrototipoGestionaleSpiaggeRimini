<?php

require_once(__ROOT__ . '/models/AuthLink.php');

class AuthLinkController{
    function verify () {
        // Verifica se l'utente ha visitato il link di autenticazione
        if (isset($_GET['token']) && isset($_GET['expires'])) {
            $token = $_GET['token'];
            $expiration_date = $_GET['expires'];
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
                $user->validated = 1;
                $user->save();

                $query = "DELETE FROM AuthLink WHERE token = :token";
                $sth = DB::get()->prepare($query);
                $sth->execute([
                    'token' => $token,
                ]);

                echo "User autenticato";
                // Cancella il token e la data di scadenza dal database o dalla sessione
                unset($_SESSION['auth_token']);
                unset($_SESSION['auth_expires']);
            }
        }
    }
}
