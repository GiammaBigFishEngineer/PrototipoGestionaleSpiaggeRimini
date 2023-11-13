<?php

require_once('BaseModel.php');

/*
Questo user model definsice una linea generale sulla realizazzione User
Cambiare in base alle esigenze.
Il metodo login salva in sessione delle variabili, ATTENZIONE: la sessione deve
partire dall'index.php anche se l'utente non è loggato. I dati dell'utente saranno estratti
dalla sessione.
Signup Control controlla la validità della registrazione.
*/
class UserModel extends BaseModel
{
    public static string $nome_tabella = 'Users';
    protected array $_fields = [
        "id",
        "fullname",
        "email",
        "password",
        "loggedIn",
        "validated",
        "cliente"
    ];


    public static function login($password,$email) {
        // Query per recuperare l'hash della password dal database
        $stmt = DB::get()->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se è stata trovata una corrispondenza nella tabella degli utenti
        if ($user && $user["validated"] == 1) {
            $password_hash = $user["password"];

            // Verifica se la password immessa corrisponde all'hash memorizzato
            if (password_verify($password, $password_hash)) {
                // Autenticazione riuscita
                // Crea una sessione o imposta un cookie per mantenere l'autenticazione
                $_SESSION['email'] = $email;
                $_SESSION['loggedIn'] = true;
                $_SESSION['userId'] = $user["id"];
                //aggiorno db per segnare l'utente loggato
                $query = "UPDATE Users SET loggedIn = :loggedIn  WHERE email = :email";
                $sth = DB::get()->prepare($query);
                $sth->execute([
                    'email' => $email,
                    'loggedIn'=> 1,
                ]);

                // Risponde con un codice di successo e i dati dell'utente
                
                echo "Autenticazione riuscita";

            } else {
                // Autenticazione non riuscita
                // Risponde con un codice di errore
                http_response_code(401);
                $message = "Password non corretta";
                echo $message;
                 //header("Location: "."");
            }
        } else {
            // Autenticazione non riuscita
            // Risponde con un codice di errore
            http_response_code(401);
            $message = "Email non esistente o non autenticata";
            echo $message;
        }
    }  

    public static function signup_control ($email): int
    {
        
        $control_qr = "SELECT * FROM Users WHERE email = :email";
        $stmt = DB::get()->prepare($control_qr);
        $stmt->execute([
            'email'=>$email,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $message = "Questa email é già utilizzata";
            echo $message;
            return 1;
        }
        return 0;
    }
}