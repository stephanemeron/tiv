<?php
class pretElement extends TIVElement {
  var $_blocs;
  var $_detendeurs;
  var $_stabs;
  var $_field_to_retrieve;
  function pretElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#personne";
    $this->_parent_url_label = "<img src='images/personne.png' /> Plongeurs/inspecteurs TIV";
    $this->_creation_label = "Création d'un nouveau prêt";
    $this->_update_label = "Mettre à jour le prêt";
    $this->_field_to_retrieve = array(
      "personne"  => "nom",
      "stab"      => "CONCAT('Réf: ', id, ' - ', modele, ' - taille : ', taille)",
      "detendeur" => "CONCAT('Réf: ', id, ' - ', modele)",
      "bloc"      => "CONCAT('Réf: ', id, ' (n° club : ', id_club, ') capacité : ', ".
                     "capacite, ' - ', constructeur, ' ', marque, ' - ', numero)");
    $this->_elements = array("id" => "Réf.", "id_personne" => "Nom de l'emprunteur", "debut_pret" => "Date de début du prêt",
                             "fin_prevu" => "Fin prévu du prêt", "fin_reel" => "Fin réel du prêt", "etat" => "Status du prêt");
    $etat_possible = array("", "Sortie", "Rentré");
    $this->_forms = array(
      "id_personne"  => array("required", "text", "Nom de l'emprunteur"),
      "id_bloc"      => array("required", "text", "Référence du bloc"),
      "id_stab"      => array("required", "text", "Référence de la jaquette"),
      "id_detendeur" => array("required", "text", "Référence du détendeur"),
      "debut_pret"   => array("required", "date", "Date de début du prêt"),
      "fin_prevu"    => array("required", "date", "Date de fin prévu du prêt"),
      "fin_reel"     => array("required", "date", "Date réelle de fin du prêt"),
      "etat"         => array("required", $etat_possible, "Status du prêt"),
    );
    $this->_forms_rules = '
  debug: false,
  rules: {
    id_personne: {
        required: true,
    },
    debut_pret: {
        date: true,
        required: true,
    },
    fin_prevu: {
        date: true,
        required: true,
    },
    etat: {
        required: true,
    },
  }';
  }
  function updateRecord(&$record) {
    $date_prevu = strtotime($record["fin_prevu"]);
    if($record["etat"] == "Rentré") {
      $record["etat"] = "<div class='ok'>".$record["etat"]."</div>";
      return;
    }
    if($date_prevu < time()) {
      $record["fin_prevu"] = "<div class='warning'>".$record["fin_prevu"]."</div>";
      $record["etat"] = "<div class='warning'>".$record["etat"]."</div>";
    } else {
      $record["fin_prevu"] = "<div class='ok'>".$record["fin_prevu"]."</div>";
      $record["etat"] = "<div class='warning'>".$record["etat"]."</div>";
    }
  }
  function getDBQuery() {
    return "SELECT pret.id,nom as id_personne,debut_pret,fin_prevu,fin_reel,etat FROM pret,personne WHERE personne.id = id_personne";
  }
  function getDBCreateQuery($id) {
    return "INSERT INTO ".$this->_name."(id,debut_pret,fin_prevu) VALUES($id,SYSDATE(), DATE_ADD(SYSDATE(), INTERVAL 31 DAY))";
  }
  function getFormInput($label, $value) {
    if(preg_match("/id_(.*)/", $label, $tmp)) {
      $db_query = "SELECT id,".$this->_field_to_retrieve[$tmp[1]]." FROM ".$tmp[1];
      $db_result = $this->_db_con->query($db_query);
      $options = array("0" => " --- Pas de ".$tmp[1]." ---");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = $result[1];
      }
      return $this->constructSelectInputLabels($label, $options, $value);
    }
    return parent::getFormInput($label, $value);
  }
}
?>