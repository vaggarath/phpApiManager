<?php

abstract class Table_Parameters {

    const TYPE = "Object";                                // typage de l'information publique

    const VIEW = "praticiens";               // vue qui présente les données. Vide si non utilisée

    const TABLE = "tab_prat";                               // table principale pour écriture

    const ACCESSIBILITY = array(                            // paramètres possibles pour accès : public, private, admin, none
        'CREATE'    => 'private',
        'READ'      => 'public',
        'UPDATE'    => 'private',
        'DELETE'    => 'private',
    );

    const PARAMETERS = array(                               // paramètres possibles pour une modification ou un ajout (UPDATE, INSERT) (seulement les pramètres liés directement à la table)
        array(
            'name'  => 'id',                                // nom retourné pour usage postérieur
            'alias' => array('id'),                         // classer les alias dans l'ordre de priorité
            'type'  => 'numeric',                           // type de valeur attendue
            'champ' => 'id',                       // correspondance en base de données
            'rename'=> 'PratId',                        // nom affiché pour usage publique
            'needs'  => array('UPDATE','DELETE'),           // cas où ce paramètre est obligatoire pour une action (CREATE,READ,UPDATE,DELETE)
        ),
        array(
            'name'  => 'nom',
            'alias' => array('nom'),
            'type'  => 'string',
            'champ' => 'nom',
            'rename'=> 'nom',
            'needs'  => array('CREATE'),
        ),
        array(
            'name'  => 'adresse',
            'alias' => array('adresse'),
            'type'  => 'string',
            'champ' => 'adresse',
            'rename'=> 'adresse',
            // 'needs'  => array(),
        ),
        array(
            'name'  => 'lat',
            'alias' => array('lat'),
            'type'  => 'string',
            'champ' => 'lat',
            'rename'=> 'lat',
            // 'needs'  => array(),
        ),
        array(
            'name'  => 'longi',
            'alias' => array('longi'),
            'type'  => 'string',
            'champ' => 'longi',
            'rename'=> 'longi',
            // 'needs'  => array(),
        ),
        array(
            'name'  => 'horaires',
            'alias' => array('horaires'),
            'type'  => 'string',
            'champ' => 'horaires',
            'rename'=> 'horaires',
            // 'needs'  => array(),
        ),
        array(
            'name'  => 'notes',
            'alias' => array('notes'),
            'type'  => 'string',
            'champ' => 'notes',
            'rename'=> 'notes',
            // 'needs'  => array(),
        ),
        array(
            'name'  => 'categorie',
            'alias' => array('categorie'),
            'type'  => 'string',
            'champ' => 'categorie',
            'rename'=> 'categorie',
            // 'needs'  => array(),
        ),
        array(
            'name'  => 'phone',
            'alias' => array('phone'),
            'type'  => 'string',
            'champ' => 'phone',
            'rename'=> 'phone',
            // 'needs'  => array(),
        )
    );


    const PUBLIQUE = array(                                 // liste des champs pour un usage publique (READ)
        'id', 'nom', 'adresse', 'lat', 'longi', 'horaires', 'phone' // 'notes', 'categorie',
    );
 
    const ORDERING = array(                                 // liste des champs de tri pour un usage publique (READ)
        'id'
    );
 
    const DISPLAY_TABLE = array(                               // mise en forme du tableau des données retrounées
        // array(
        //     'init'=>'batimentId',
        //     'long'=>8,
        //     'name'=>'Batiment',
        // ),
    );

    const DISPLAY_LISTING = array(                            // mise en forme d'une liste d'enregistrements où le début est identique
        // array(
        //     'init'=>1,
        //     'long'=>1,
        //     'name'=>'niveaux',
        // ),
    );
}



?>
