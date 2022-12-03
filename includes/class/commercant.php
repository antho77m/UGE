<?php
class commercant { // Création de la classe commercant
                public $SIREN;
                public $raison_social;
                public $nb_transaction;
                public $montant;
                public $date;

                function __construct($SIREN, $raison_social, $nb_transaction, $montant, $date){ // Constructeur de la classe commercant
                    $this->SIREN = $SIREN;
                    $this->raison_social = $raison_social;
                    $this->nb_transaction = $nb_transaction;
                    $this->montant = $montant;
                    $this->date = $date;
                }
}
?>