<?php
class detendeurElement extends TIVElement {
  function detendeurElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#materiel";
    $this->_parent_url_label = "<img src='images/materiel.png' /> Matériel";
    $this->_creation_label = "Création d'un détendeur";
    $this->_update_label = "Mettre à jour le détendeur";
    $this->_elements = array("id" => "Réf.", "modele" => "Modèle", "id_1ier_etage" => "n° 1ier étage",
                             "id_2e_etage" => "n° 2ieme étage", "id_octopus" => "n° octopus",
                             "date_achat" => "Date d'achat", "observation" => "Observation/Remarques");
    $this->_forms = array(
      "modele"         => array("required", "number", "Modèle de détendeur"),
      "id_1ier_etage"  => array("required", false,    "Référence constructeur du 1ier étage"),
      "id_2e_etage"    => array("required", false,    "Référence constructeur du 2ieme étage"),
      "id_octopus"     => array("required", false,    "Référence constructeur de l'octopus"),
      "date_achat"     => array("required", "date",   "Date d'achat du détendeur"),
      "observation"    => array("required", "text",   "Observation/Remarques"),
    );
    $this->_forms_rules = '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    id_1ier_etage: {
        required: true,
    },
    id_2e_etage: {
        required: true,
    },
    id_octopus: {
        required: true,
    },
    date_achat: {
        required: true,
        date: true
    },
  }';
  }
}
?>