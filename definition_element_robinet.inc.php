<?php
class robinetElement extends TIVElement {
  function robinetElement($db_con = false) {

    global $bloc_filetage;
    global $robinet_filetage_sortie;
    global $spec_robinet;

    parent::__construct($db_con);
    $this->_show_delete_form = true;
    $this->_creation_label = "Création d'un robinet";
    $this->_update_label = "Mettre à jour le robinet";
    $this->_parent_url       = "/?pills=materiel";
    $this->_parent_url_label = "<i class='fa fa-wrench'></i> Matériel";
    //$this->_element_to_link = "id";
    $this->_elements =  array("id" => "Réf.", "marque" => "Marque", "serial_number"=>" Numéro de série",  "filetage" => "Filetage",
                              "nb_sortie" => "Nombre de sortie(s)", "filetage_sortie" => "Filetage de sortie", "spec_robinet"=>"Spec SS, DSA, DSS", "net_ultrason" => "Nettoyage ultrason", "observation" => "Observations/Remarques");
    //$spec_robinet = array("SS", "DSA", "DSS");
    //$robinet_filetage_sortie = array("G5/8 ISO 228-1", "M26x2 (Nitrox)");
    $this->_hidden_column_sm = array("operations");

    $this->_readonly_column = array("marque", "serial_number", "filetage", "nb_sortie", "filetage_sortie", "spec_robinet");
    $this->_forms = array(
      "marque"        => array("required", "number", "Marque du robinet"),
      "serial_number" => array("required", "text", "Numéro de série du robinet"),
      "filetage"      => array("required", "radio", "Pas du filetage", $bloc_filetage ),
      "nb_sortie"     => array("required", "radio", "Nombre de sortie(s)",array(1,2) ),
      "filetage_sortie" => array("required", "radio", "Filetage de sortie", $robinet_filetage_sortie ),
      "net_ultrason"     => array("required", "boolean" , "Nettoyage ultrason"),
      "spec_robinet"     => array("required","radio", "Spec", $spec_robinet ),
      "observation"   => array("required", "text" , "Observations/Remarques"),
    );
    $this->_forms_rules = '
  "debug": false,
  "rules": {
    "marque": {
        "required": true
    },
    "serial_number": {
        "required": true
    },
    "filetage": {
        "required": true
    },
    "nb_sortie": {
        "required": true
    },
    "filetage_sortie": {
        "required": true
    }
  }';
  }
}
?>
