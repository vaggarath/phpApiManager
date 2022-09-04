<?php

abstract class TableRepository {

    public static function setInBdd($src) {
        // préparation des paramètres optionnels
            $getParam = Outil::testMultiParametreRequis(Table_Parameters::PARAMETERS,$src);
            unset($getParam['id']);

            // si on obtient des valeurs à modifier incluses dans le tableau $getParam
            if (is_array($getParam) && !empty($getParam)){
                // préparation d'un tableau de valeurs
                $contenu = array();
                array_walk($getParam,function($val,$key)use(&$contenu){
                    $contenu[":".$key]=$val;
                });
                // préparation de la liste des champs
                $champs = Outil::CreateTableau('name','champ');
                $fields = array_keys($getParam);
                array_walk($fields, function(&$item)use($champs){
                    return $item = $champs[$item];
                });
                
                // la requête : ajoute les valeurs de l'objet
                    $rch = "INSERT INTO ".ApiManager_Parameters::SCHEMA.Table_Parameters::TABLE." ";
                    $rch .= "(".implode(',', $fields).") ";
                    $rch .= "VALUES (".implode(', ', array_keys($contenu)).") ";

                try {
                    $requete = connectBDD::getLink()->prepare($rch);
                    // Valeur du retour = résultat de la requête
                    return $requete->execute($contenu);
                    // return true;

                }catch(Exception $e){
                    Message::errorStructurelle();
                }

            }else{
                Message::errorBadRequest();
                return false;
            }

    }


    public static function delFromBdd($lid): bool {
        if (!empty($lid) && $lid>0){
            try {
                $champs = Outil::CreateTableau('name','champ');
                // recherche les valeurs de l'objet
                $contenu = array(':lid'=>$lid);
                $rch = "DELETE FROM ".ApiManager_Parameters::SCHEMA.Table_Parameters::TABLE." ";
                $rch .= "WHERE ".$champs['id']."=:lid";
                $requete = connectBDD::getLink()->prepare($rch);
                // Valeur du retour = résultat de la requête
                return $requete->execute($contenu);

            }catch(Exception $e){
                Message::errorStructurelle();
            }

        }else{
            Message::errorBadRequest();
            return false;
        }
    }


    public static function updateInBdd( int $lid, array $src=array()): bool {

        if (!empty($lid) && $lid>0){                
            // préparation des paramètres optionnels
            $getParam = Outil::testMultiParametreRequis(Table_Parameters::PARAMETERS,$src);
            // si on obtient des valeurs à modifier incluses dans le tableau $getParam
            if (is_array($getParam) && !empty($getParam)){
                // préparation d'un tableau de valeurs
                $contenu = array(':id'=>$lid);
                array_walk($getParam,function($val,$key)use(&$contenu){
                        $contenu[":".$key]=$val;
                });

                // préparation de la liste des champs
                $champs = Outil::CreateTableau('name','champ');
                $fields = array_keys($getParam);
                array_walk($fields, function(&$item)use($champs){
                    return $item = $champs[$item]."=:".$item;
                });

                try {
                    // la requête : modifie les valeurs de l'objet
                    $rch = "UPDATE ".ApiManager_Parameters::SCHEMA.Table_Parameters::TABLE." ";
                    $rch .= "SET ".implode(',', $fields)." ";
                    $rch .= "WHERE ".$champs['id']."=:id ";
                    $requete = connectBDD::getLink()->prepare($rch);
                    // Valeur du retour = résultat de la requête
                    return $requete->execute($contenu);

                }catch(Exception $e){
                    Message::errorStructurelle();
                }

            }else{
                Message::errorBadRequest();
                return false;
            }
            
        }else{
            Message::errorBadRequest();
            return false;
        }
    }
}



?>
