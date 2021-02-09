<?php
class robinetElement extends TIVElement {
  function robinetElement($db_con = false) {
    parent::__construct($db_con);
    $this->_show_delete_form = true;
    $this->_creation_label = "Création d'un robinet";
    $this->_update_label = "Mettre à jour le robinet";
    $this->_parent_url       = "./#materiel";
    $this->_parent_url_label = "<img src='images/materiel.png' /> Matériel";
    $this->_elements =  array("id" => "Réf.", "marque" => "Marque", "serial_number"=>" Numéro de série",  "filetage" => "Filetage",
                              "nb_sortie" => "Nombre de sortie(s)", "filetage_sortie" => "Filetage de sortie", "spec_robinet"=>"Spec SS, DSA, DSS", "net_ultrason" => "Nettoyage ultrason", "observation" => "Observations/Remarques");
    $spec_robinet = array("SS", "DSA", "DSS");
    $robinet_filetage_sortie = array("G5/8 ISO 228-1", "M26x2 (Nitrox)");
    $this->_forms = array(
      "marque"        => array("required", "number", "Marque du robinet"),
      "serial_number" => array("required", "text", "Numéro de série du robinet"),
      "filetage"      => array("required", $this->bloc_filetage , "Pas du filetage"),
      "nb_sortie"     => array("required", array(1,2) , "Nombre de sortie(s)"),
      "filetage_sortie" => array("required", $robinet_filetage_sortie , "Filetage de sortie"),
      "net_ultrason"     => array("required", "boolean" , "Nettoyage ultrason"),
      "spec_robinet"     => array("required",$spec_robinet , "Spec"),
      "observation"   => array("required", "text" , "Observations/Remarques"),
    );
    $this->_forms_rules = '
  debug: false,
  rules: {
    marque: {
        required: true,
    },
    serial_number: {
        required: true,
    },
    filetage: {
        required: true
    },
    nb_sortie: {
        required: true
    },
    filetage_sortie: {
        required: true
    },
  }';
  }
}
?>