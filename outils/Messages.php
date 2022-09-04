<?php

class Message  {
    private static function retour(string $type='html', $message='') {
        switch ($type){
            case 'html':
            case 'text/html':
                header('Content-Type: text/html');
                echo $message; 
            break;
            
            case 'txt':
            case 'text':
            case 'texte':
            case 'text/plain':
                header('Content-Type: text/plain');
                echo $message; 
            break;
            
            case 'json':
                header('Content-Type: application/vnd.api+json');
                echo json_encode($message); 
            break;
            
            case 'xml':
                header('Content-Type: text/xml');
                echo Listing::xml_encode($message);
            break;

            default:
                header('Content-Type: text/html');
                // echo "essais";
        }
    }


    
    //  Validation

    public static function validation(string $message="", string $type='html') {
        http_response_code(200);
        self::retour($type,$message);
        die;
    }

    // 201 	Created
    public static function validationCreated($message="", string $type='html') {
        http_response_code(201);
        self::retour($type,$message);
        die;
    }

    //  Errors
    
    // 204
    public static function validationNoContent(
        string $message="Aucun résultat sur ce serveur ... La requête a bien été reçue mais elle n'a pas produit de résultat", 
        string $type='html'
        ) {
            http_response_code(204);
            self::retour($type,$message);
            // die;
    }

    //  code 400 	Bad Request
    public static function errorBadRequest(
        string $message="La requête n'a pas pu être réalisée : paramètres insuffisants ou erronés.",
        string $type='html'
        ) {
            http_response_code(400);
            self::retour($type, $message);
            // die;
        }


    //  code 401 	Unauthorized
    public static function errorNoIdentification(
        string $message="Une authentification est nécessaire pour accéder à la ressource.",
        string $type='html'
        ) {
            http_response_code(401);
            self::retour($type, $message);
            // die;
        }


    //  code 405 	Method Not Allowed
    public static function errorNotAllowed(
        string $message="Méthode de requête non autorisée.",
        string $type='html'
        ) {
            http_response_code(405);
            self::retour($type, $message);
            // die;
        }


    //  code 409 	Conflict
    public static function errorConflicts(
        string $message="La requête n'a pas pu être réalisée.",
        string $type='html'
        ) {
            http_response_code(409);
            self::retour($type, $message);
            // die;
        }

    //  code 412 	Precondition Failed
    public static function errorPreconditions(
        string $message="Préconditions envoyées pour la requête non vérifiées : paramètres insuffisants ou erronés.",
        string $type='html'
        ) {
            http_response_code(412);
            self::retour($type, $message);
            die;
        }

    //  code 421 	Bad mapping
    public static function errorStructurelle(
        string $message="La requête n'a pas pu être réalisée sur le serveur.",
        string $type='html'
        ) {
            http_response_code(421);
            self::retour($type, $message);
            die;
        }

    //  500 	Internal Server Error
    public static function errorServer(
        string $message="Erreur interne du serveur.",
        string $type='html'
        ) {
            http_response_code(500);
            self::retour($type, $message);
            die;
        }

}


?>