<?php
include_once("configuration.inc.php");

function get_element_handler($element, &$db_con) {
  $class_element = $element."Element";
  $to_retrieve = "\$edit_class = new $class_element(\$db_con);";
  eval($to_retrieve);
  return $edit_class;
}

class TIVElement {
  // Nom de l'élément (correspond à une table)
  var $_name;
  // tableau temporaire contenant les valeurs d'un élément en base
  var $_values;
  // Connexion vers la base
  var $_db_con;
  // Compteur du nombre d'enregistrement détectés
  var $_record_count;
  // Tableau contenant la liste des classes a utiliser pour l'affichage des lignes d'un tableau
  var $_tr_class;
  // Tableau associatif contenant un label pour les champs de la table associée au type
  var $_elements;
  // Tableau associatif contenant des informations sur les champs de la table du type
  var $_forms;
  // Chaîne javascript contenant des instructions de vérification pour un formulaire
  var $_forms_rules;
  // Entier indiquant si on doit couper l'affichage des éléments sur plusieurs colonnes
  var $_form_split_count;
  // Label du formulaire d'édition
  var $_update_label;
  // Label formulaire de création
  var $_creation_label;
  // Label d'édition
  var $_edit_label;
  // Label suppression
  var $_delete_label;
  // Message affiché lors d'une suppression
  var $_delete_message;
  // Affichage du bouton de suppression dans le formulaire
  var $_show_delete_form;
  // Affichage en read only ?
  var $_read_only;
  // Affichage du bouton de création au dessus de la liste ?
  var $_show_create_form;
  // Forcer l'affichage de certains éléments caché par défaut ? (ex: blocs rebutés)
  var $_force_display;
  // Url parente (gestion navigation)
  var $_parent_url;
  // Label url parente
  var $_parent_url_label;
  // Titre de la liste
  var $_url_title_label;
  // Label d'affichage lors d'une édition
  var $_legend_label;
  // Tableau indiquant les liens entre les élement du formulaire (pression épreuve et service par exemple)
  var $_form_dependency;
  // Constructeur principal
  function TIVElement($db_con = false) {
    // Init chaîne de texte
    $this->_name = str_replace("Element", "", get_class($this));
    $this->_update_label     = "Mettre à jour le/la ".$this->_name;
    $this->_url_title_label  = "<img src='images/liste.png' /> Liste des ".$this->_name."s";
    $this->_legend_label     = "Édition du ".$this->_name." __ID__";
    $this->_back_url         = "affichage_element.php?element=".$this->_name;
    $this->_parent_url       = "./";
    $this->_parent_url_label = "<img src='images/accueil.png' /> Accueil";
    $this->_form_dependency  = array();
    $this->_form_split_count = 0;
    $this->_record_count = 0;
    $this->_tr_class = array("odd", "even");
    $this->_db_con = $db_con;
    $this->_elements = array();
    $this->_forms = array();
    $this->_forms_rules = "";
    $this->_creation_label = "Création d'un(e) ".$this->_name;
    $this->_edit_label = "Édition d'un élément (".$this->_name.")";
    $this->_delete_label = "Supprimer cet élément";
    $this->_delete_message = "Lancer la suppression ?";
    $this->_show_delete_form = false;
    $this->_read_only = false;
    $this->_show_create_form = true;
    $this->_force_display = false;
  }
  function getTableName() {
    global $db_table_prefix;
    if(isset($db_table_prefix)) {
      return $db_table_prefix.$this->_name;
    } else {
      return $this->_name;
    }
  }
  function setDBCon($db_con) {
    $this->_db_con = $db_con;
  }
  function getElements() { return array_keys($this->_elements); }
  function getHeaderElements() { return array_values($this->_elements); }
  function getFormsRules() { return $this->_forms_rules; }
  function getForms() { return $this->_forms; }
  function getFormsKey() { return array_keys($this->_forms); }
  function constructTextInput($label, $size, $value, $class = false) {
    $form_input = "<input type=\"text\" name=\"$label\" id=\"$label\" size=\"$size\" value=\"$value\"".
                  ($class ? "class=\"$class\"": "")." />\n";
    return $form_input;
  }
  function constructSelectInputLabels($label, $labels, $value) {
    $form_input = "<select id=\"$label\" name=\"$label\">\n";
    foreach(array_keys($labels) as $option) {
      $selected = ($option == $value ? " selected='selected'" : "");
      $form_input .= "<option value='$option'$selected>".$labels[$option]."</option>\n";
    }
    $form_input .= "</select>\n";
    // Gestion de la dépendance entre élément du formulaire.
    if(array_key_exists($label, $this->_form_dependency)) {
      $form_input .= "<script>\n$('#$label').change(function() {\n";
      $tmp = $this->_form_dependency[$label];
      $dependency = array_keys($tmp);
      $link = $dependency[0];
      $linked_values = $tmp[$link];
      foreach($linked_values as $key=>$value) {
        $form_input .= "if($('#$label').val() == '$key') {\n";
        $form_input .= "  $('#$link').val($value);\n";
        $form_input .= "}\n";
      }
      $form_input .= "});\n</script>";
    }
    return $form_input;
  }
  function constructSelectInput($label, $options, $value) {
    $labels = array();
    foreach($options as $opt) { $labels[$opt] = $opt; }
    return $this->constructSelectInputLabels($label, $labels, $value);
  }
  function constructDateInput($label, $value) {
    $form_input = "
    <script>
    $(function() {
      $( \"#$label\" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        appendText: '(yyyy-mm-dd)',
      });
      $( \"#$label\" ).datepicker({ altFormat: 'yyyy-mm-dd' });
    });
    </script>\n";
    $form_input .= $this->constructTextInput($label, 10, $value);
    return $form_input;
  }
  function updateRecord(&$record) {
    return false;
  }
  function getDBQuery() {
    return "SELECT ".join(",", $this->getElements())." FROM ".$this->getTableName();
  }
  function getDBCreateQuery($id) {
    return "INSERT INTO ".$this->getTableName()."(id) VALUES($id)";
  }
  function createDBRecord() {
    $db_result =  $this->_db_con->query("SELECT max(id) + 1 FROM ".$this->getTableName());
    $tmp = $db_result->fetch_array();
    $id = $tmp[0];
    if(!$id) $id = 1; // Si table vide assignation id à 1

    if($this->_db_con->query($this->getDBCreateQuery($id))) {
      //add_journal_entry($this->_db_con, $id, $this->_name, "Création");
      return $id;
    }
    return false;
  }
  function deleteDBRecord($id) {
    $db_result =  $this->_db_con->query("DELETE FROM ".$this->getTableName()." WHERE id = '$id'");

    if($db_result) {
      add_journal_entry($this->_db_con, $id, $this->_name, "Suppression");
      return $db_result;
    }
    return false;
  }
  function updateDBRecord($id, &$values) {
    $db_query = "SELECT ".implode(",", $this->getFormsKey())." FROM ".$this->getTableName()." WHERE id=$id";
    $db_result = $this->_db_con->query($db_query);
    if(!$result = $db_result->fetch_array()) {
      return false;
    }

    $to_set = array();
    foreach($this->getFormsKey() as $field) {
      if(strcmp($values[$field], $result[$field]) != 0) {
        $to_set[]= "$field = '".$this->_db_con->escape_string($values[$field])."'";
      }
    }
    if(count($to_set) > 0) {
      add_journal_entry($this->_db_con, $id, $this->_name, "Lancement d'une mise à jour (".implode(",", $to_set).")");
      $result = $this->_db_con->query("UPDATE ".$this->getTableName()." SET ".implode(",", $to_set)." WHERE id = '$id'");
      return 1;
    }
    return 2;
  }
  function isDisplayed(&$record) {
    return true;
  }
  function getAdditionalControl() {
    if($this->_read_only || !$this->_show_create_form) return "";
    return '<form name="ajout_form" id="ajout_form" action="ajout_element.php" method="POST">
<input type="hidden" name="element" value="'.$this->_name.'" />
<input type="submit" name="submit" onclick=\'return(confirm("Procéder à la création ?"));\' value="'.$this->_creation_label.'" />
</form>
';
  }
  function getHTMLTable($id, $label, $db_query = false, $show_additional_control = true) {
    $table = $this->getJSOptions($id, $label);
    if($show_additional_control)
      $table .= $this->getAdditionalControl($id);
    $table .= "<table class='table table-striped table-bordered' style='width:100%' id='$id'>\n";
    $table .= "  <thead>".$this->getHTMLHeaderTable()."</thead>\n";
    $table .= "  <tbody>\n";
    if(!$db_query) $db_query = $this->getDBQuery();
    $db_result =  $this->_db_con->query($db_query);
    $this->_record_count = 0;
    while($line = $db_result->fetch_array()) {
      if(!$this->isDisplayed($line) && !$this->_force_display) continue;
      $current_class = $this->_tr_class[$this->_record_count++ % count($this->_tr_class)];
      // Met à jour l'état de la ligne courante afin de rajouter des informations
      // et renvoie une classe d'affichage css en cas de modification
      // pour mettre en avant un bloc ayant passé sa date de TIV par exemple.
      $table .= $this->getHTMLLineTable($line, $current_class);
    }

    $table .= "  </tbody>\n";
    $table .= "  <tfoot>".$this->getHTMLHeaderTable()."</tfoot>\n";
    $table .= "</table>\n";
    return $table;
  }
  function getJSOptions($id, $label, $display = 25) {
    return "<script type='text/javascript' charset='utf-8'>
  $(document).ready(function() {
    $('#$id').dataTable();
  } );
</script>\n";
  }
  function getHTMLHeaderTable() {
    $header = "    <tr>\n      <th>";
    $header .= join("</th><th>", $this->getHeaderElements());
    if(!$this->_read_only) $header .= "</th><th>Opérations";
    $header .= "</th>\n    </tr>\n";
    return $header;
  }
  function getHTMLLineTable(&$record, $default_class) {
    $current_class = $default_class;
    if($tmp = $this->updateRecord($record)) {
      $current_class = $tmp;
    }
    $line = "    <tr class=\"$current_class\">\n      <td>";
    $id = $record[0];
    $to_display = array();
    foreach($this->getElements() as $elt) {
      $to_display []= $record[$elt];
    }
    if(!$this->_read_only) {
      $to_display [] = $this->getEditUrl($id);
    }
    $line .= implode("</td><td>", $to_display);
    $line .= "</td>\n    </tr>\n";
    return $line;
  }
  function getExtraInformation($id) {
  }
  function getExtraOperation($id) {
  }
  function getURLReference($id) {
    return "id=$id&element=".$this->_name;
  }
  function getEditUrl($id) {
    $element_to_manage = $this->getURLReference($id);
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";
    return "<a href='edit.php?$element_to_manage' title=\"Éditer cet élément\">".
           "<img src='images/edit.png' style='vertical-align:middle;' /></a> / ".
           "<a style='color: #F33;' onclick='$delete_confirmation' title='Supprimer cet élément (confirmation nécessaire)' href='delete.php?$element_to_manage'>".
           "<img src='images/delete.png' style='vertical-align:middle;' /></a>";
  }
  function getParentUrl() {
    return "Navigation : <a href='./'><img src='images/accueil.png' /> Accueil</a> > \n".
           "<a href='".$this->_parent_url."'>".$this->_parent_url_label."</a>";
  }
  function getQuickNavigationFormInput() {
    $input  = " > Navigation rapide<select name='id' onchange='this.form.submit()'>\n".
              "<option></option>\n";
    $db_result = $this->_db_con->query("SELECT id FROM ".$this->getTableName()." ORDER BY id");
    while($result = $db_result->fetch_array()) {
      $selected = ($result['id'] == $_GET['id'] ? " selected" : "");
      $input .= "<option value='".$result['id']."'$selected>".$this->_name." ".$result['id']."</option>\n";
    }
    $input .= "</select></p>";
    return $input;
  }
  function getNavigationUrl() {
    $input_form = $this->getQuickNavigationFormInput();
    return "<form action='edit.php' method='GET' style='display: inline!important;'>\n".
           "<input type='hidden' name='element' value='".$this->_name."' />\n".
           "<p>".$this->getParentUrl()." > \n<a href='".$this->getBackUrl()."'>".$this->getUrlTitle()."</a>\n$input_form</p>\n</form>\n";
  }
  function getBackUrl() {
    return $this->_back_url;
  }
  function getUrlTitle() {
    return $this->_url_title_label;
  }
  function getEditLabel() {
    return $this->_edit_label;
  }
  function getLegend($id) {
    return str_replace("__ID__", $id, $this->_legend_label);
  }
  function getElementLabel($label, $value) {
    $forms_definition = $this->getForms();
    return $forms_definition[$label][2];
  }
  function getFormInput($label, $value) {
    $forms_definition = $this->getForms();
    $form_input = "";
    if(is_array($forms_definition[$label][1])) {
      $form_input = $this->constructSelectInput($label, $forms_definition[$label][1], $value);
    } elseif($forms_definition[$label][1] === "select") {
      $form_input = $this->constructSelectInput($label, $forms_definition[$label][3], $value);
    } elseif($forms_definition[$label][1] === "date") {
      $form_input = $this->constructDateInput($label, $value);
    } elseif($forms_definition[$label][1] === "tags") {
      $tags = join(",", $forms_definition[$label][3]);
      $form_input = "<script type=\"text/javascript\">\n".
                    "\$(document).ready(function() { \$('#$label').magicSuggest({
                data: '$tags',allowFreeEntries:false,maxDropHeightinteger:'100px',maxSuggestions:10
            });
         });
     </script>".
     "<input id=\"$label\" style=\"width:350px;\" type=\"text\" name='$label' value='$value' />\n";
    } else {
      $form_input = $this->constructTextInput($label, 30, $value);
    }
    return $form_input;
  }
  function getUpdateLabel() {
    return $this->_update_label;
  }
  function retrieveValues($id) {
    $db_query = "SELECT ".implode(",", $this->getFormsKey())." FROM ".$this->getTableName()." WHERE id = $id";
    $db_result =  $this->_db_con->query($db_query);
    return $db_result->fetch_array();
  }
  function constructEditForm($id, $form_name, $action = "") {
    $this->_values = $this->retrieveValues($id);
    if(!$this->_values) return false;
    $form  = "<form name='$form_name' id='$form_name' action='$action' method='POST'>\n";
    $form .= "<input type='hidden' name='id' value='$id' />\n";
    $form .= "<input type='hidden' name='element' value='".$this->_name."' />\n";
    $form .= "<table>\n";
    $form .= "  <tbody>\n";
    $i = 0;
    $columns = array();
    foreach($this->getFormsKey() as $elt) {
      $value = $this->_values[$elt];
      if(!isset($columns[$i])) $columns[$i] = "";
      $columns[$i++] .= "<td>".$this->getElementLabel($elt, $value)."</td>".
                        "<td>".$this->getFormInput($elt, stripcslashes($value))."</td>";
      if($this->_form_split_count && $i > $this->_form_split_count) $i = 0;
    }
    $form .= "<tr>".join("</tr>\n<tr>", $columns)."</tr>";
    $form .= "  </tbody>\n";
    $form .= "</table>\n";
    $form .= "<span style='height:0; width:0; overflow: hidden;'>\n";
    $form .= "  <button type='submit' value='default action' />\n";
    $form .= "</span>\n";

    if($this->_show_delete_form) {
      $form .= "<input type='hidden' name='embedded' value='1' />\n"; // Utilisé pour détecter une suppression depuis le formulaire
      $form .= "<input type='submit' style='background: red;' name='delete' ".
               "value='".$this->_delete_label."' />\n";
    }
    $form .= "<input type='submit' name='lancer' value='".$this->getUpdateLabel()."' />\n";
    $form .= "</form>\n";
    return $form;
  }
}

include_once("definition_element_bloc.inc.php");
include_once("definition_element_robinet.inc.php");
include_once("definition_element_detendeur.inc.php");
include_once("definition_element_stab.inc.php");
include_once("definition_element_inspecteur_tiv.inc.php");
include_once("definition_element_personne.inc.php");
include_once("definition_element_pret.inc.php");
include_once("definition_element_inspection_tiv.inc.php");
include_once("definition_element_journal_tiv.inc.php");

?>