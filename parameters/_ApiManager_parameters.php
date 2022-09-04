<?php
// $apiTitle = $_ENV['DB_TYPE'];
abstract class ApiManager_Parameters {

    const TITLE = "Api"; // change this

    const CORRESPONDANCE = array (  
        'praticiens'    => 'Praticiens'
    );

    const SCHEMA = "";// nom du schema dans la base PostGre. Vide = schema public

    const APINORMAGE = array(
        'project'=>"Api",
        'url'=>'',
    );
}



?>