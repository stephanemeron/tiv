<?php
class detendeurElement extends TIVElement {
  function detendeurElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#materiel";
    $this->_parent_url_label = "<i class='fa fa-wrench'></i> Matériel";
    $this->_creation_label = "Création d'un détendeur";
    $this->_update_label = "Mettre à jour le détendeur";
    $this->_hidden_column = array("id");
    $this->_elements = array("id" => "ID", "id_club" => "Réf.", "marque" => "Marque","etat_1ier_etage"=> "1ier étage", "id_1ier_etage" => "n° 1ier étage",
                             "etat_2e_etage"=> "2ème étage", "id_2e_etage" => "n° 2ieme étage", "etat_octopus"=> "Octopus",
                             "id_octopus" => "n° octopus", "etat_direct_system"=> "Direct-system", "etat_mano"=> "Manomètre", "embout_enfant" => "Embout enfant", "eaux_froides" => "Eaux froides", "date_achat" => "Date d'achat", "observation" => "Observation/Remarques");
    $this->_forms = array(
      "id_club"             => array("required", false, "Réf."),
      "marque"              => array("required", false, "Marque de détendeur"),
      "etat_1ier_etage"     => array("required", false,    "1ier étage"),
      "id_1ier_etage"       => array("required", false,    "Référence constructeur du 1ier étage"),
      "etat_2e_etage"       => array("required", false,    "2ème étage"),
      "id_2e_etage"         => array("required", false,    "Référence constructeur du 2ieme étage"),
      "etat_octopus"        => array("required", false,    "Octopus"),
      "id_octopus"          => array("required", false,    "Référence constructeur de l'octopus"),
      "etat_direct_system"  => array("required", false,    "Direct-system"),
      "etat_mano"           => array("required", false,    "Mano"),
      "embout_enfant"       => array("required", false, "Embout enfant"),
      "eaux_froides"        => array("required", "boolean",    "Eaux froides"),
      "date_achat"          => array("required", "date",   "Date d'achat du détendeur"),
      "observation"         => array("required", "text",   "Observation/Remarques")
    );
    $this->_forms_rules = '
  "debug": true,
  "rules": {
    "id_club": {
        "required": true
    },
    "marque": {
        "required": true
    },
    "etat_1ier_etage": {
        "required": true
    },
    "id_1ier_etage": {
        "required": true
    },
    "etat_2e_etage": {
        "required": true
    },
    "id_2e_etage": {
        "required": true
    },
    "etat_octopus": {
        "required": true
    },
    "id_octopus": {
        "required": true
    },
    "etat_direct_system": {
        "required": true
    },
    "etat_mano": {
        "required": true
    },
    "embout_enfant": {
        "required": true
    },
    "eaux_froides": {
        "required": true
    },
    "date_achat": {
        "required": true,
        "date": true
    }
  }';
  }
}
?>
