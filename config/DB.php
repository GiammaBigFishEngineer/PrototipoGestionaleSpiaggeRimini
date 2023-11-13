<?php

require_once('config.php');

class DB
{
    private static $instance = null;

    public static function get()
    {
        if (self::$instance == null) {
            $uri = "mysql:host=" . Config::$db_host  . ";dbname=" . Config::$db_name;

            try {
                self::$instance = new PDO(
                    $uri,
                    Config::$db_username,
                    Config::$db_password,
                    array(
                        PDO::ATTR_PERSISTENT => true
                    )
                );
            } catch (PDOException $e) {
                // Handle this properly
                throw $e;
            }
        }

        return self::$instance;
    }
}
