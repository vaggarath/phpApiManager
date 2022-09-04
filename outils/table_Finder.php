<?php

abstract class TableFinder {

    // liste complète (avec sélection des champs)
    // -----------------------------------------------------
    
    public static function getListing(array $data) : array {
        if (!empty($data) && $data['projet']=="Medicis"){
            try {
                $champs = Outil::CreateTableau('name','champ');
                $filterChamps = Outil::CreateTableau('rename','champ');
                $filterType = Outil::CreateTableau('rename','type');

                // sélection de la liste
                // ---------------------
                
                $contenu = array();
                // conditions de sélection (notamment sur l'id)
                $condition = array();
                if (key_exists('id',$data) && !empty($data['id'])){
                    $key = ':lid';
                    $contenu[$key] = $data['id'];
                    $condition[':lid'] = $champs['id'].'='.$key;
                }

                // conditions de filtrage
                if (key_exists('filter',$data)){
                    $temp = str_replace( array('"', '='),   '', $data['filter'] ) ;
                    $temp = '{"'.str_replace( array(':', ','),array('":"', '","'),$temp ) .'"}';
                    $filterListe = json_decode($temp,true);
                    $filterListe0 = $filterListe;
                    
                    if (is_array($filterListe)){
                        // traitement pour les conditions de la requête SQL, différentié selon numeric/string
                        array_walk ($filterListe, function(&$val,$key)use($filterChamps,$filterType){
                            if (key_exists($key,$filterChamps)){
                                if ($filterType[$key]=='numeric'){
                                    $val = $filterChamps[$key]." = :".$key;
                                }else{
                                    $val = $filterChamps[$key]." LIKE '%".$val."%' ";
                                }
                            }else{
                                $val = NULL;
                            }
                        });

                        $filterListeOk = array_filter($filterListe,function($val){
                            return !(is_null($val));
                        });
                        $condition = array_merge($condition, $filterListeOk ); 

                        // traitement pour le contenu de chaque condition
                        foreach($filterListeOk as $key => $val){
                            if ($filterType[$key]=='numeric'){
                                $key0 = ':'.$key;
                                $contenu[$key0.''] = $filterListe0[$key];
                            }
                        }
                    }
                }

                if (!empty($condition) ){$condition = 'WHERE '.implode(' AND ',$condition).' ';}else{$condition=' ';}


                // tri de la liste
                // ----------------
                $order = '';
                if (is_array(Table_Parameters::ORDERING) && !empty(Table_Parameters::ORDERING)){
                    $order = array_map(function($e)use($champs){
                        if (key_exists($e,$champs)){
                            return $champs[$e];
                        }
                    },Table_Parameters::ORDERING);
                }
                if (!empty($order)) {$order = 'ORDER BY '.implode(', ',$order);}

                // recherche les champs/valeurs de l'objet à afficher
                // --------------------------------------------------
                $champsVisibles = array_map(function($e)use($champs){
                    return $champs[$e];
                },Table_Parameters::PUBLIQUE);
                
                // requête SQL
                // -----------------
                $rch =  "SELECT   ".implode(',',$champsVisibles).' ';
                $rch .= "FROM     ".ApiManager_Parameters::SCHEMA.Table_Parameters::VIEW.' ';
                $rch .= $condition;
                $rch .= $order;
                $requete = connectBDD::getLink()->prepare($rch);
                $requete->execute($contenu);

                // validation
                // ----------------
                $count = $requete->rowCount();
                if ($count>0){
                    $resultat =  $requete->fetchAll(PDO::FETCH_ASSOC);
                    // indication du type de données
                    $resultat = array_map(function($e){
                        return array_merge( array('type'=>Table_Parameters::TYPE), $e );
                    }, $resultat);
                    
                    // renommer les champs souhaités pour un renvoi propre
                    return Outil::rename($resultat);
                }else{
                    Message::validationCreated();
                    return array();
                }

            }catch(Exception $e){
                Message::errorStructurelle(); 
            }

        }else{
            Message::errorBadRequest();
            return array();
        }
    }


    // cherche le dernier enregistrement réalisé
    // -----------------------------------------------------
    
    public static function getLatest() : array {
        try {
            $champs = Outil::CreateTableau('name','champ');
            // recherche les valeurs de l'objet
            $rch = "SELECT    ".$champs['id'].' ';
            $rch .= "FROM     ".ApiManager_Parameters::SCHEMA.Table_Parameters::TABLE.' ';
            $rch .= "ORDER BY ".$champs['id']." DESC LIMIT 1 ";
            $requete = connectBDD::getLink()->prepare($rch);
            $requete->execute();
            // validation
            $count = $requete->rowCount();
            if ($count>0){
                // requête valide : retourne son résultat
                $resultat =  $requete->fetch(PDO::FETCH_ASSOC);
                // renomage
                $r0 = Outil::rename(array($resultat));
                return $r0[0];
            }else{
                Message::validationNoContent();
                return array();
            }

        }catch(Exception $e){
            Message::errorStructurelle();
        }
    }
    
}
 

?>
