<?php
/*
 * DISPATCHER BASATO SU MVC, OGNI URL USA UN CONTROLLER PER ACCEDERE 
 * AL MODELLO E INTERFACCIARSI CON UNA VIEW
*/
/*
    Mostra errori se online:
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
*/
/*
    ESempio di routing:
    case '/lista_clienti':
        LeadController::list();
        break;
*/

define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__ . '/controllers/configura_spiaggia.php');
require_once(__ROOT__ . '/controllers/settings.php');
require_once(__ROOT__ . '/controllers/dashboard.php');
require_once(__ROOT__ . '/controllers/pages.php');
require_once(__ROOT__ . '/users/login.php');
require_once(__ROOT__ . '/users/signup.php');
require_once(__ROOT__ . '/users/auth.php');
require_once(__ROOT__ . '/users/forgot_password.php');

session_start();

class Dispatcher
{
    private $method;
    private $path;

    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    public function dispatch()
    {
        if( isset($_SESSION['userId']) ){
            switch ($this->path) {

                case '/logout':
                    session_destroy();
                    header('Location: ' . $GLOBALS['url_frontend'].'login');
                    break;

                // Pagina di mappatura della spiaggia
                
                case '/configura_spiaggia':
                    $controller = ConfSpiaggeController::configura_spiaggia();
                    break;
                case '/aggiungi_riga':
                    $controller = ConfSpiaggeController::aggiungi_riga();
                    break;
                case '/riempi_riga':
                    $controller = ConfSpiaggeController::riempi_riga();
                    break;
                case '/elimina_riga':
                    $controller = ConfSpiaggeController::elimina_riga();
                    break;
                case '/aggiorna_ombrellone':
                    $controller = ConfSpiaggeController::aggiorna_ombrellone();
                    break;
                case '/settings':
                    $controller = SettingsController::lista_spiagge();
                    break;
                case '/registra_spiaggia':
                    $controller = SettingsController::crea_spiaggia();
                    break;
                case '/elimina_spiaggia':
                    $controller = SettingsController::elimina_spiaggia();
                    break;
                case '/prenota_ombrellone':
                    $controller = ConfSpiaggeController::prenota_ombrellone();
                    break;
                case '/dashboard':
                    $controller = DashboardController::dashboard();
                    break;
                case '/crea_evento':
                    $controller = DashboardController::crea_evento();
                    break;
                case '/carica_files':    
                    $id = $_POST['spiaggiaId'];
                    $controller = new UploadFiles();
                    $path = "spiagge_file/";
                    $controller::upload($path,$id);
                    //header('Location: /dashboard');
                    break;
                    
                default:
                    echo "404 HTML<br>";
                    echo $this->path;
                    break;
        }
            
    } else {
        switch ($this->path) {
            // User not in Section --------------------
            case '/info_spiaggia':
                $controller = new PagesController ();
                $controller::infoSpiaggia();
                break;
            case '/ricerca_spiaggia':
                $controller = new PagesController ();
                $controller::ricercaSpiagge();
                break;
            case '/auth':
                $controller = new AuthLinkController();
                $controller->verify();
                break;
            case '/forgot_password':
                $controller = new ForgotPassword();
                $controller::send();
                break;
            case '/change_password':
                $controller = new ForgotPassword();
                $controller::change();
                break;
            case '/login':
                $controller = new Login();
                $controller->send();
                break;
            case '/signup':
                $controller = new Signup();
                $controller->send();
                break;
            default:
                header('Location: /login');
                break;
        }
    }
    }
}

$dispatcher = new Dispatcher();
$dispatcher->dispatch();
