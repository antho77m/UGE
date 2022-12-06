<?php
class commercant { // Création de la classe commercant
    public $SIREN;
    public $raison_social;
    public $nb_transaction;
    public $montant;
    public $date;

    public function __construct($SIREN, $raison_social, $nb_transaction, $montant, $date){ // Constructeur de la classe commercant
        $this->SIREN = $SIREN;
        $this->raison_social = $raison_social;
        $this->nb_transaction = $nb_transaction;
        $this->montant = $montant;
        $this->date = $date;
    }

    // fonctions get
    public function getSIREN(){
        return $this->SIREN;
    }
    public function getRaison_social(){
        return $this->raison_social;
    }
    public function getNb_transaction(){
        return $this->nb_transaction;
    }
    public function getMontant(){
        return $this->montant;
    }
    public function getDate(){
        return $this->date;
    }
}
?>