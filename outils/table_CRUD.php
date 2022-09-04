<?php
 
class Table_CRUD  extends Listing {

    protected $lid = NULL;

    public function __construct(array $data=array()){
        if (!empty($data)){
            // - tester les paramètres obligatoires
            if (key_exists('projet',$data)){
                if (key_exists('id',$data)){
                    $this->setId($data['id']);
                }
                // recherche les valeurs de l'objet
                $this->liste = TableFinder::getListing($data);

                // mise en forme : tableaux dans des clés
                if (Table_Parameters::DISPLAY_TABLE!==NULL && is_array(Table_Parameters::DISPLAY_TABLE) && !empty(Table_Parameters::DISPLAY_TABLE)){
                    array_map(function($item){
                        $this->liste = Outil::groupeSousTableau($this->liste, $item['init'], $item['long'], $item['name']);
                    },Table_Parameters::DISPLAY_TABLE);
                }

                if (Table_Parameters::DISPLAY_LISTING!==NULL && is_array(Table_Parameters::DISPLAY_LISTING) && !empty(Table_Parameters::DISPLAY_LISTING)){
                    array_map(function($item){
                        $this->liste = Outil::separateurSousTableau($this->liste,$item['long'],$item['name'],$item['init']);
                    },Table_Parameters::DISPLAY_LISTING);
                }

                // suppression des contenus vides
                $this->liste = $this->filtreVide($this->liste) ;
            }
        }
    }
    private function filtreVide($data){
        $temp = array_map(function($item){
            $t = $item;
            if (is_array($item) && !empty($item)){$t=$this->filtreVide($item);}
            return $t;
        },$data);
        return array_filter($temp,function($item){
            if (is_null($item)){return false;}
            if (is_string($item) && trim($item)==''){return false;}
            if (is_array($item) && empty($item)){return false;}
            return true;
        });

    }

    private function getId()        { return $this->lid; }
    private function setId ($val)   { $this->lid = floor($val); }

    
    //  *   méthodes liées à la base de données   *
    //  *-----------------------------------------*

    // mise à jour d'un enregistrement de la base de données
    // -----------------------------------------------------

    public function insertInBdd($src){
            // - tester les paramètres obligatoires
            $getParam = Outil::testMultiParametreRequis(Outil::getNeeds('CREATE'),$src);
            
            // - contrôle de compatibilité
            if (count(Outil::getNeeds('CREATE'))!=count($getParam)){ Message::errorPreconditions();}
 
            // - enregistrement - retour : true/false
            $poste = TableRepository::setInBdd($src);

            // - Message retourné : identifiant du nouvel enregistrement
            if ($poste){
                // chercher l'index du nouvel enregistrement
                $latest = TableFinder::getLatest();
                if (!empty($latest)) {
                    Message::validationCreated($latest,'json');
                }else{
                    Message::validationNoContent();
                }
            }else{
                Message::validationNoContent();
            }

    }

    // mise à jour d'un enregistrement de la base de données
    // -----------------------------------------------------

    public function updateInBdd($src){

        // - l'enregistrement existe dans la liste (à la construction)
        if (is_null( $this->getId() )){    
            Message::errorBadRequest("Aucun résultat sur ce serveur ... La référence est inconnue."); 
            return;
        }
        
        // - tester les paramètres obligatoires : renvoi false ou un tableau ('son nom'=>'sa valeur')
        $getParam = Outil::testMultiParametreRequis(Outil::getNeeds('UPDATE'), $src);
            
        // - contrôle de compatibilité
        if (count(Outil::getNeeds('UPDATE'))!=count($getParam)){ 
            Message::errorPreconditions();
            return;
        }
        
        // enregistrement - retour : true/false
        $poste = TableRepository::updateInBdd($this->getId(), $src);

        // - Message retourné : identifiant du nouvel enregistrement
        if ($poste){
            Message::validation();
        }else{
            Message::validationNoContent();
        }
        return;
    }

    // suppression d'un enregistrement de la base de données
    // -----------------------------------------------------

    public function deleteInBdd($src){

        // - l'enregistrement existe dans la liste (à la construction)
        if (is_null( $this->getId() )){    
            Message::errorBadRequest("Aucun résultat sur ce serveur ... La référence est inconnue."); 
            return;
        }
        
        // - tester les paramètres obligatoires : renvoi false ou un tableau ('son nom'=>'sa valeur')
        $getParam = Outil::testMultiParametreRequis(Outil::getNeeds('DELETE') , $src);
        // suppression            
        if (TableRepository::delFromBdd( $this->getId() )){
            Message::validation();
        }else{
            Message::validationNoContent();
        }
        return;
    }

}

?>
