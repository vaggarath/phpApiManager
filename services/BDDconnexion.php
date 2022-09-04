<?php

class ConnectBDD
{
    protected static $connexion;
    
    public static function getLink() {        
        try {
                static::$connexion = new PDO(
                    $_ENV['DB_TYPE'].':host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'].';dbname='.$_ENV['DB_NAME'],
                    $_ENV['DB_USER'],
                    ""
                );
                return static::$connexion;
            
            return static::$connexion;
        } catch (Exception $e) {
            echo ("connection impossible ... Veuillez contacter l'administrateur<br>");
        }
    }
    public static function getServeur()  {
        return $_ENV['DB_HOST'];
    }

}

?>