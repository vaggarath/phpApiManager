<?php

class Outil  {

    // préparation d'un tableau combinant 2 champs de la constante PARAMETERS d'une table

    public static function CreateTableau(string $key, string $val) {
        $rename_keys = array_map(function($item)use($key){
            return $item[$key];
        },Table_Parameters::PARAMETERS);
        $rename_value = array_map(function($item)use($val){
            if (key_exists($val,$item)){
                return $item[$val];
            }else{
                return NULL;
            }
        },Table_Parameters::PARAMETERS);
        return array_combine($rename_keys,$rename_value);
    }

    // filtre les champs de PARAMETERS selon une clé dans 'needs'
    public static function getNeeds($key)   { 
        return array_filter(Table_Parameters::PARAMETERS,function($item)use($key){
            if (key_exists('needs',$item)){
                return in_array($key,$item['needs']);
            }
            return array();
        });
    }


    // renommer les champs d'un tableau $data dont les clés sont dans le tableau $rename
    
    public static function rename(array $data) {
        $rename = SELF::CreateTableau('champ','rename');

        return array_map(function($item)use($rename){
            $key = array_keys($item);
            $val = array_values($item);
            array_walk($key,function(&$e, $k)use($rename){
                if (key_exists($e,$rename)){$e = $rename[$e];}
            } );
            return array_combine($key,$val);
        }, $data);

    }

    // fonction utile pour l'user
    public static function renameSeul(array $item, array $rename) {
            $key = array_keys($item);
            $val = array_values($item);
            array_walk($key,function(&$e, $k)use($rename){
                if (key_exists($e,$rename)){$e = $rename[$e];}
            } );
            return array_combine($key,$val);

    }



    // découpage par key ou index 
    // pour obtenir un tableau dans le tableau
    // à partir d'une liste/tableau
    // la première partie étant commune, la seconde partie s'insère dans la première à un point précisé
    // **  data : le tableau
    // **  separateur : position numérique ou nom d'une clé/key
    // **  name : nom de la clé qui reçoit le sous-tableau
    // **  init : point de départ dans le tableau d'origine (data), ce qui est avant n'apparaît pas dans le résultat final

    public static function separateurSousTableau(array $data, $separateur=1, string $name='liste', int $init=0) {
        // vérification que le séparateur est numérique ou un clé valide
        if (empty($data)){return array();}
        if (is_numeric($separateur)){$sep=$separateur;}
        if (is_string($separateur)){$sep = array_search($separateur, array_keys($data[0])); }
        if (!is_numeric($sep) || $sep<=0 || !is_int($sep)){return array();}

        $rs = array();
        $anc = NULL;
        $lid = -1;
        foreach($data as $r){
            // repérage de chaque 1ère partie identique du tableau 
            $temp = array_slice($r,$init,$sep);
            if ($temp!==$anc){
                $lid++;
                $anc = $temp;
                $rs[$lid] = $temp;
                $rs[$lid][$name] = array();
            }
            // repérage de la fin du tableau (2e partie de la requête)
            $listeFin = array_slice($r,$sep);
            // mémorisation
            array_push($rs[$lid][$name], $listeFin);
        }
        return $rs;
    }

    public static function groupeSousTableau(array $data, $init=1, int $long=1, string $name='liste') {
        $temp  = array_map(function($item)use($init,$long,$name){
            $deb = -1;
            if (is_string($init)) {
                $rename = SELF::CreateTableau('name','rename');
               if (key_exists($init,$item)){
                   $deb = array_search($init,array_keys($item) );
                }elseif (key_exists($init,$rename)){
                    $deb = array_search($rename[$init],array_keys($item) );
                }
            }
            if (is_int($init) && $init>=0){$deb = $init;}

            if ($deb>-1){
                $temp = array_slice($item,0,$deb);
                $temp[$name] = array_slice($item,$deb,$long);
                return array_merge($temp, array_slice($item,$deb+$long) );

            }else{
                return $item;
            }

        },$data);
        
        return $temp;
        // return ;
    }


    //  contrôle la présence de plusieurs paramètres envoyés 
    //  chaque paramètre est une composante d'un tableau/array
    //  gère les paramètres multiple et appelle testParametreRequis() ci-dessous
    //  **  needs : tableau contenant des tableaux normés (name,alias,type) cf. ci-dessous
    //  **  methode : méthode d'envoi des données (GET par défaut, POST, PUT, DELETE ; autre : échec )

    public static function testMultiParametreRequis(array $needs, array $src=array()) {
        $r = array_map(function($e)use($src){
            return self::testParametreRequis($e,$src);
        },$needs);
   
        $r = array_reduce($r,function($acc, $e){
            if(!is_array($acc)){$acc=array();}
            if (is_array($e)){return array_merge($acc,$e);}
            return $acc;
        });

        return $r;
    }


    //  contrôle la présence de paramètres envoyés 
    //  pour faire ensuite une action dans de bonnes conditions
    //  **  needs : tableau contenant des tableaux normés (name,alias,type) cf. ci-dessous
    //  **  src : tableau contenant des données (peut recevoir $_GET, $_POST, autre tableau personnel )

    public static function testParametreRequis(array $needs, array $src=array()) {
        // Comment normer le tableau $needs ?
        // exemple :
        // $needs = array(
        //     'name'  => 'eval',                                      // nom retourné pour usage postérieur
        //     'alias' => array('fk_evaluation','evaluation','eval'),  // classer les alias dans l'ordre de priorité
        //     'type'  => 'numeric',                                   // type de valeur attendue
        // );
        // si mal normé ou contenu inexistant : retourne false

        if (empty($needs) || empty($src)){ return false;}

        // teste l'existence des différents alias de paramètres
        if (!key_exists('alias',$needs)){ return false;}
        $temp = array_reduce($needs['alias'], function($acc, $e)use($src){
            if ($e=='user'){ return strval($_SESSION['id']) ; }
            if (key_exists($e,$src)){ 
                if ($src[$e]===0){return '00';}
                return $src[$e]; 
            }            
            return $acc;            
        });

        if (empty($temp)){ return false;}
        // if ($needs['name']=='user'){return array('user'=>strval($_SESSION['id']) );}

        // vérifier le type de données
        switch ($needs['type']){
            case 'int':
            case 'integer':
            case 'numeric':
                if (!is_numeric($temp)){return false;}
            break;
            case 'string':
                if (!is_string($temp)){return false;}
            break;
            default :
                return false;
        }

        return array($needs['name']=>$temp);
    }

}
