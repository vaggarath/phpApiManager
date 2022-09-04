<?php

abstract class Listing {
    protected $liste = array();       // php array
    protected $listeJson = array();   // array for json

    
// tests function
    public function testExists(){
        return !(empty($this->liste));
    }

    // display functions

    public function getListe(): array { 
        return $this->liste;
    }

    public function getListeJson() { 
        $this->prettifyJson();
        return json_encode($this->listeJson); 
    }

    protected function prettifyJson() { 
        $this->listeJson = array_merge(
            array('data'=>$this->liste),
            // ApiManager_Parameters::APINORMAGE
            array(
                'project'=>$_ENV['API_NAME'],
                'url'=>$_ENV['API_URL'],
            )
        );
    }

    public function getListeByField($val=0,string $field='id'): array { 
        $lieu = Table_Parameters::CHAMPS[$field];
        $lieu = Table_Parameters::RENAME[$lieu];
        return array_filter($this->liste,function($e)use($val,$lieu){
            if (key_exists($lieu,$e) && $e[$lieu]==$val){ return true;}
            return false;
        });
    }
    // return json
    public function senderListeJson() { 
        $this->prettifyJson();
        Message::validationCreated($this->listeJson, 'json');
    }

}
