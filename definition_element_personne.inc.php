<?php
class personneElement extends TIVElement {
  function personneElement($db_con = false) {

    global $qualifications_label;
    global $niveau;
    global $assurance;

    parent::__construct($db_con);
    $this->_parent_url       = "/?pills=personne";
    $this->_parent_url_label = "<i class='fa fa-user-circle-o'></i>  Plongeurs/inspecteurs TIV";
    $this->_creation_label = "Création d'une nouvelle personne";
    $this->_update_label = "Mettre à jour la personne";
    $this->_show_delete_form = true;
    $this->_elements = array("id" => "Réf.", "CONCAT(prenom,' ',nom)" => "Prénom Nom", "adresse" => "Adresse de la personne",
                             "CONCAT(
                                     CONCAT(IF(telephone_domicile,' domicile : ', ''), telephone_domicile,
                                            IF(telephone_portable,' portable : ', ''), telephone_portable),
                                     IF(telephone_bureau, ' bureau : ', ''), telephone_bureau)" =>
                             "Téléphone domicile/portable/bureau", "is_admin" => "Est admin", "is_super_admin" => "Est SUPER admin");

    $this->_forms = array(
      "groupe"                => array("required", "text", "Groupe"),
      "licence"               => array("required", "text", "n° de licence"),
      "prenom"                => array("required", "text", "Prénom"),
      "nom"                   => array("required", "text", "Nom"),
      "adresse"               => array("required", "text", "Adresse"),
      "code_postal"           => array("required", "text", "Code postal"),
      "ville"                 => array("required", "text", "Ville"),
      "telephone_domicile"    => array("required", "text", "Téléphone domicile"),
      "telephone_portable"    => array("required", "text", "Téléphone portable"),
      "telephone_bureau"      => array("required", "text", "Téléphone bureau"),
      "email"                 => array("required", "text", "Adresse mail"),
      "password"              => array(false, "password", "Mot de passe"),
      "date_naissance"        => array("required", "date", "Date de naissance"),
      "lieu_naissance"        => array("required", "text", "Lieu de naissance"),
      "niveau"                => array("required", "select", "Niveau plongeur", $niveau),
      "date_obtention_niveau" => array("required", "date", "Date d'obtention du niveau"),
      "nombre_plongee"        => array("required", "integer", "Nombre de plongée"),
      "date_derniere_plongee" => array("required", "date", "Date dernière plongée"),
      "type_assurance"        => array("required", "select", "Type d'assurance", $assurance),
      "qualifications"        => array("required", "select", "Qualifications supplémentaires", $qualifications_label),
      "is_admin"              => array("required",  "boolean", "Est admin", 0),
      "is_super_admin"        => array("required",  "boolean", "Est SUPER admin", 0)

    );
    $this->_form_split_count = 6;
    $this->_forms_rules = '
      "debug": false,
      "rules": {
        "licence": {
          "required": true
        },
        "nom": {
            "required": true
        },
        "prenom": {
            "required": true
        },
        "adresse": {
            "required": true
        },
        "code_postal": {
            "required": true
        },
        "ville": {
            "required": true
        }
      }';
  }
  function getQuickNavigationFormInput() {
    $input  = " > Navigation rapide : <select name='id' onchange='this.form.submit()'>\n".
              "<option></option>\n";
    $db_result = $this->_db_con->query("SELECT id,CONCAT(prenom,' ',nom) as nom FROM ".$this->getTableName()." ORDER BY prenom, nom");
    while($result = $db_result->fetch_array()) {
      $selected = ($result['id'] == $_GET['id'] ? " selected" : "");
      $input .= "<option value='".$result['id']."'$selected>".$result['nom']." (id n° ".$result['id'].")</option>\n";
    }
    $input .= "</select></p>".
              "</form>";
    return $input;
  }

  function updateDBRecord($id, &$values) {
    $db_query = "SELECT ".implode(",", $this->getFormsKey())." FROM ".$this->getTableName()." WHERE id=$id";
    $db_result = $this->_db_con->query($db_query);
    if(!$result = $db_result->fetch_array()) {
      return false;
    }

    $to_set = array();
    foreach($this->getFormsKey() as $field) {
        if($field == 'password'){
            if(strcmp(hash('sha512',substr($values['licence'],5)), $result[$field]) != 0) {
                $to_set[]= "$field = '".$this->_db_con->escape_string(hash('sha512',substr($values['licence'],5)))."'";
            }
        }
        else{
          if(strcmp($values[$field], $result[$field]) != 0) {
            $to_set[]= "$field = '".$this->_db_con->escape_string($values[$field])."'";
          }
      }
    }
    if(count($to_set) > 0) {
      add_journal_entry($this->_db_con, $id, $this->_name, "Lancement d'une mise à jour (".implode(",", $to_set).")");
      $result = $this->_db_con->query("UPDATE ".$this->getTableName()." SET ".implode(",", $to_set)." WHERE id = '$id'");
      return 1;
    }
    return 2;
  }
}
?>
