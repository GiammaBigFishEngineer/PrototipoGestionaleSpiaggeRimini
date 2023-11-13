<?php

function send_auth_link($user,$email) {
    // Genera un token casuale per il link di autenticazione
    $token = bin2hex(random_bytes(16));
    
    // Calcola la data e l'ora di scadenza del link
    $expiration_time = time() + 3600; // 3600 secondi = 1 ora
    $expiration_date = date('Y-m-d H:i:s', $expiration_time);
    
    // Costruisci il link di autenticazione con il token e la data di scadenza
    $auth_link = "http://localhost:4000/auth?token=$token&expires=$expiration_date";
    
    // Invia un'email all'indirizzo specificato con il link di autenticazione
    $to = $email;
    $subject = "Link di autenticazione";
    $message = "Clicca sul seguente link per autenticarti: $auth_link";
    $headers = "From: webmaster@example.com" . "\r\n" .
               "Reply-To: webmaster@example.com" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    mail($to, $subject, $message, $headers);
    
    // Salva il token e la data di scadenza nel database o in una sessione
    $_SESSION['auth_token'] = $token;
    $_SESSION['auth_expires'] = $expiration_time;
    $data = array(
        "id" => null,
        "token" => $token,
        "user" => $user,
        "expiration" => $expiration_date
    );
    $auth = new AuthLink($data);
    $id = $auth->save();
    echo $auth_link;
}