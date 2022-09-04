<?php

session_start();

require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try
{ 
    require_once('outils/Messages.php');
    require('parameters/_ApiManager_Parameters.php');

    // test if the given api exists
    if (key_exists('api',$_GET)) {
        $latable = strtolower($_GET['api']); 
        if (key_exists($latable,ApiManager_Parameters::CORRESPONDANCE)){
            $latable = ApiManager_Parameters::CORRESPONDANCE[$latable];
        }else{Message::errorBadRequest();die;}
    }else{Message::errorBadRequest();die;}

    // test l'existence de la table indiquée en paramètre
    $latable_url = "parameters/".$latable."_Parameters.php";
    if (!file_exists($latable_url)){Message::errorStructurelle();}
    require_once($latable_url);

    // importations
    require_once('outils/Outils.php');
    require_once("outils/Listing.php");
    require_once('services/BDDconnexion.php');

    require_once("outils/table_CRUD.php");
    require_once("outils/table_Finder.php");
    require_once("outils/table_Repository.php");

    // new connection
    new ConnectBDD;

    // Méthodes de requête utilisée (POST, GET, PUT,...)
    
    switch ($_SERVER['REQUEST_METHOD']) 
    {  
        // Méthode POST: Envoi de données
        case 'POST': 
            // source de l'info : POST ou un json ?
            $src = $_POST;
            if (key_exists('data',$src)){
                $jsonData = json_decode($src['data'],true);
                if (json_last_error()===JSON_ERROR_NONE){
                    $src = $jsonData;
                }
            }
            // avec id spécifié : modification de l'enregistrement
            if (key_exists('id',$_POST) || key_exists('id',$src)){
                if (key_exists('id',$_POST) ){ $lid = $_POST['id']; }
                if (key_exists('id',$src)   ){ $lid = $src['id'];   }else{ $src['id']=$_POST['id']; }
                $monEtude = new Table_CRUD([ 'projet'=>'Medicis','id'=>$lid ]);
                if ($monEtude->testExists()){
                    $monEtude->updateInBdd($src);
                }else{
                    Message::errorBadRequest();
                }
            }else{
                // sans id spécifié : ajout dans la BDD
                $monEtude = new Table_CRUD(['projet'=>'Medicis']);
                $monEtude->insertInBdd($src);
            }
        break;

        // Méthode GET: récupération de données
        case 'GET': 
            // - tester les paramètres obligatoires : renvoie false ou un tableau ('son nom'=>'sa valeur')
            $champs = Outil::getNeeds('READ');
            // paramètres de sélection : initialisation
            $rchParam = array('projet'=>'Medicis');
            // paramètres de sélection : présence de l'id
            if ( empty($champs)  ){ 
                if (key_exists('id',$_GET)){
                    // étude sur un seul enregistrement
                    $rchParam['id'] = $_GET['id'];
                }
            }else{
                $getParam = Outil::testMultiParametreRequis( $champs, $_GET);
                // - paramètres bien reçus
                if ( !is_array($getParam) || count($getParam)!=count($champs) ){ Message::errorBadRequest();break;}
                if (key_exists('id',$getParam)){
                    // étude sur un seul enregistrement
                    $rchParam['id'] = $getParam['id'];
                }
            }
            // paramètres de sélection : présence de l'id
            if (key_exists('filter',$_GET)){
                // étude sur un seul enregistrement
                $rchParam['filter'] = $_GET['filter'];
            }
            
            // créer l'objet dont le contenu est la sélaction des données
            $monEtude = new Table_CRUD($rchParam);

            if ($monEtude->testExists()){
                $monEtude->senderListeJson();
            }
       break;
        
        // Méthode DELETE: suppression de données
        case 'DELETE': 
            // - tester les paramètres obligatoires : renvoi false ou un tableau ('son nom'=>'sa valeur')
            $getParam = Outil::testMultiParametreRequis(Outil::getNeeds('DELETE') , $_GET);

            // - paramètres bien reçus
            if ($getParam && is_array($getParam) && !empty($getParam) && key_exists('id',$getParam)){
                // suppression
                $monEtude = new Table_CRUD(array('projet'=>'Medicis','id'=>$getParam['id'] ) );
                $monEtude->deleteInBdd($_GET);
            }else{
                Message::errorBadRequest();
            }

        break;
        
        // Méthode PUT: remplace les données
        case 'PUT': 
            // - tester les paramètres obligatoires : renvoi false ou un tableau ('son nom'=>'sa valeur')
            $getParam = Outil::testMultiParametreRequis(Outil::getNeeds('UPDATE') , $_GET);

            // - paramètres bien reçus
            if ($getParam && is_array($getParam) && !empty($getParam) && key_exists('id',$getParam)){
                // mise à jour
                $monEtude = new Table_CRUD([ 'projet'=>'Medicis','id'=>$getParam['id'] ]);
                $monEtude->updateInBdd($_GET);
            }else{
                Message::errorBadRequest();
            }
            
        break;
    };

} catch (Throwable $m){
    Message::errorServer($m);
};

?>