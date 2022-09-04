<?php

abstract class Listing {
    protected $liste = array();       // tableau au format php
    protected $listeJson = array();   // tableau avec les normes Json pour api

    
    // ***   les fonctions de test    ***
    // ----------------------------------

    public function testExists(){
        return !(empty($this->liste));
    }

    // ***  les fonctions d'affichage ***
    // ----------------------------------
   
    // renvoi le tableau au format php
    public function getListe(): array { 
        return $this->liste;
    }
    
    // renvoi un objet/tableau Json avec les normes Json pour api
    public function getListeJson() { 
        $this->prettifyJson();
        return json_encode($this->listeJson); 
    }

    // Normer le tableau php 'liste' pour sortie d'une api
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

    // renvoi un tableau au format php avec une condition sur un champs
    public function getListeByField($val=0,string $field='id'): array { 
        $lieu = Table_Parameters::CHAMPS[$field];
        $lieu = Table_Parameters::RENAME[$lieu];
        return array_filter($this->liste,function($e)use($val,$lieu){
            if (key_exists($lieu,$e) && $e[$lieu]==$val){ return true;}
            return false;
        });
    }
    
    //  Outils de préparation du code liste en xml
    //  ******************************************

    public static function xml_encode($mixed, $domElement = NULL, $DOMDocument = NULL) {
        if (is_null($DOMDocument)) {
            $DOMDocument = new DOMDocument;
            $DOMDocument->formatOutput = true;
     
            $rootNode = $DOMDocument->createElement('entries');
            $DOMDocument->appendChild($rootNode);
     
            self::xml_encode($mixed, $rootNode, $DOMDocument);
     
            echo @$DOMDocument->saveXML();
        } else {
            if (is_array($mixed)) {
                foreach ($mixed as $index=>$mixedElement) {
                    if (is_int($index)) {
                        $nodeName = 'entry';
                    } else {
                        $nodeName = $index;
                    }
                    $node = $DOMDocument->createElement($nodeName);
                    $domElement->appendChild($node);
                    self::xml_encode($mixedElement, $node, $DOMDocument);
                }
            } else {
                // TODO: test if CDATA if needed
                $new_node = $DOMDocument->createTextNode($mixed);
     
                $domElement->appendChild($new_node);
            }
        }
    } 
 
    // ***  les fonctions pour le Web ***
    // ----------------------------------
    
    // retourne un document Web au format Json normé
    public function senderListeJson() { 
        $this->prettifyJson();
        Message::validationCreated($this->listeJson, 'json');
    }

}
