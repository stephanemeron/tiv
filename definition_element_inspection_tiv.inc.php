<?php
class inspection_tivElement extends TIVElement {
  var $_date;
  var $_columns;
  function inspection_tivElement($db_con = false, $date = false) {
    parent::__construct($db_con);
    $this->_show_create_form = false;
    $this->_parent_url       = "./#admin";
    $this->_parent_url_label = "<i class='fa fa-sliders'></i> Administration";
    $this->_update_label = "Mettre à jour les informations sur l&#145;inspection TIV";
    $this->_elements = array(
      "id", "id_bloc", "id_inspecteur_tiv", "date", "etat_exterieur", "remarque_exterieur", "etat_interieur",
      "remarque_interieur", "etat_filetage", "remarque_filetage", "etat_robineterie", "remarque_robineterie",
      "decision", "remarque",);
    $this->_columns = array("Réf.", "Numéro du bloc", "Constructeur bloc", "Marque bloc", "Capacité bloc",
                            "Réf robinet", "Date dernière épreuve", "Date dernier TIV", "Décision", "Remarque");
    $this->_forms = array(
      "id_bloc"              => array("required", "text", "Numéro du bloc associé"),
      //"id_robinet"           => array("required", "text", "Numéro du robinet du bloc"),
      "id_inspecteur_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
      "date"                 => array("required", "date", "Date de l'inspection TIV"),
      "etat_exterieur"       => array("required", $this->getPossibleStatus(), "État externe du bloc"),
      "remarque_exterieur"   => array("required", false, "Remarque sur l'état externe du bloc"),
      "etat_interieur"       => array("required", $this->getPossibleStatus(true), "État interne du bloc"),
      "remarque_interieur"   => array("required", false, "Remarque sur l'état interne du bloc"),
      "etat_filetage"        => array("required", $this->getPossibleStatus(), "État du filetage du bloc"),
      "remarque_filetage"    => array("required", false, "Remarque sur le filetage du bloc"),
      "etat_robineterie"     => array("required", $this->getPossibleStatus(), "État de la robineterie du bloc"),
      "remarque_robineterie" => array("required", false, "Remarque sur la robineterie du bloc"),
      "decision"             => array("required", array("", "OK", "Rebuté"), "Le bloc est-il accepté ?"),
      "remarque"             => array("required", "text", "Commentaire sur l'inspection."),
    );
    $this->_forms_rules = '
  "debug": false,
  "rules": {
    "id": {
        "required": true
    },
    "id_bloc": {
        "required": true
    },
    "id_inspecteur_tiv": {
        "required": true
    },
    "date": {
        "required": true,
        "date": true,
    }
  }';
    if(!$date) {
      if(array_key_exists("date", $_GET)) {
        $date = $_GET['date'];
      } else if(array_key_exists("date", $_POST)) {
        $date = $_POST['date'];
      }
    }
    $this->_date = $date;
    $this->_url_title_label = "<i class='fa fa-list'></i> Retour à la liste des fiches d'inspections TIV du ".$this->_date;
    $this->_back_url        = "consultation_tiv.php?date_tiv=".$this->_date;
  }
  function getURLReference($id) {
    return parent::getURLReference($id)."&date=".$this->_date;
  }
  function getExtraInformation($id) {
    $db_result = $this->_db_con->query("SELECT id_bloc,id_inspecteur_tiv,date, id_club FROM inspection_tiv, bloc WHERE inspection_tiv.id = $id AND bloc.id=id_bloc");
    $result = $db_result->fetch_array();
    if(!$this->_date){
        $this->_date = $result["date"];
    }
    /*$extra_info = "<p>Navigation rapide : <a href='edit.php?id=".$result[0]."&element=bloc'>".
                  "<img src='images/edit.png' style='vertical-align:middle;' /> fiche du bloc</a> - ".
                  "<a href='edit.php?id=".$result[1]."&element=inspecteur_tiv'>".
                  "<img src='images/personne.png' style='vertical-align:middle;' /> fiche de l'inspecteur TIV</a> - ".
                  "<a href='impression_fiche_tiv.php?id_bloc=".$result[0]."&date=".$this->_date."'>".
                  "<img src='images/pdf.png' style='vertical-align:middle;' /> fiche au format PDF</a>";
                  */

    $extra_info = "<a class='btn btn-info mr-3' href='edit.php?id=".$result["id_bloc"]."&element=bloc' title='Éditer la fiche TIV'>".
                      "<i class='fa fa-eye' aria-hidden='true'></i> fiche de bloc N° ".$result["id_club"]."</a>";
                      if($result["id_inspecteur_tiv"]){
                          $extra_info.="<a class='btn btn-info mr-3' href='edit.php?id=".$result["id_inspecteur_tiv"]."&element=inspecteur_tiv'>";
                          $extra_info.="<i class='fa fa-graduation-cap' aria-hidden='true'></i> fiche de l'inspecteur TIV</a>";
                      }

                      $extra_info.="<a class='btn btn-info' href='impression_fiche_tiv.php?id_bloc=".$result["id_bloc"]."&date=".$this->_date."'>".
                      "<i class='fa fa-file-pdf-o' aria-hidden='true'></i> fiche au format PDF</a>";


    return array('bg_color' => "info", 'extra_info' => $extra_info);
  }
  function getExtraOperation($id) {
    $db_query = "SELECT date_dernier_tiv ".
                "FROM bloc,inspection_tiv WHERE inspection_tiv.id = $id AND id_bloc = bloc.id ".
                "AND decision IN ('OK', 'Rebuté') ".
                "AND (date_dernier_tiv < inspection_tiv.date OR decision != bloc.etat)";
    $db_result = $this->_db_con->query($db_query);
    if(!$db_result->fetch_array()) {
      return "<div class='ok'>Pas d'opération possible. La date de cette fiche TIV est inférieur/égale à la dernière date TIV du bloc.</div>";
    }
    $db_query = "SELECT id_bloc,decision,date FROM inspection_tiv WHERE id = $id";
    $db_result = $this->_db_con->query($db_query);
    $result = $db_result->fetch_array();
    if(!$result) {
      return "<div class='warning'>Pas d'opération supplémentaire possible. Veuillez changer la décision à 'OK' afin de pouvoir mettre à jour le bloc.</div>";
    }
    $id_bloc = $result[0];
    $date_tiv = $result[2];
    $form  = "<form name='update_bloc' id='update_bloc' action='update_bloc_tiv.php' method='POST'>\n";
    $form .= "<input type='hidden' name='date_tiv' value='$date_tiv' />\n";
    $form .= "<input type='hidden' name='blocs_to_update[]' value='$id_bloc' />\n";
    $form .= "<input type='submit' name='lancer' value='Lancer la mise à jour du bloc avec le contenu de cette fiche TIV'>\n";
    $form .= "</form>\n";
    return $form;
  }
  function setDate($date) {
    $this->_date = $date;
  }
  function getDBQuery() {
      // $db_query = "SELECT inspection_tiv.id FROM inspection_tiv WHERE inspection_tiv.date = '".$this->_date."'";
      // $db_result = $this->_db_con->query($db_query);
      // while($result = $db_result->fetch_array()) {
      //   $total++;
      //   $time = strtotime($result[1]);
      //   if($time > $max_time_tiv) $count_tiv++;
      //   else $reepreuve++;
      // }
      return "SELECT inspection_tiv.id, CONCAT('Réf :', bloc.id, ' / n° club : ', bloc.id_club), bloc.constructeur, bloc.marque, bloc.capacite, robinet.serial_number, ".
             "bloc.date_derniere_epreuve, bloc.date_dernier_tiv,decision,remarque ".
             "FROM inspection_tiv, bloc, robinet ".
             "WHERE inspection_tiv.date = '".$this->_date."' AND id_bloc = bloc.id AND robinet.id = bloc.id_robinet ".
             "ORDER BY inspection_tiv.date";

      //return "SELECT inspection_tiv.id, CONCAT('Réf :', bloc.id, ' / n° club : ', bloc.id_club), bloc.constructeur, bloc.marque, bloc.capacite, robinet.serial_number, ".
           // "inspecteur_tiv.nom, bloc.date_derniere_epreuve, bloc.date_dernier_tiv,decision,remarque ".
           // "FROM inspection_tiv, bloc, inspecteur_tiv, robinet ".
           // "WHERE inspection_tiv.date = '".$this->_date."' AND id_bloc = bloc.id AND id_inspecteur_tiv = inspecteur_tiv.id AND robinet.id = bloc.id_robinet ".
           // "ORDER BY inspection_tiv.date";
  }

  function getHTMLTable($id, $label, $db_query = false, $show_additional_control = true) {
    $table = $this->getJSOptions($id, $label);
    if($show_additional_control)
      $table .= $this->getAdditionalControl($id);
    $table .= "<div class='table-responsive'><table class='table table-striped table-bordered nowrap def-obj' id='$id'>\n";
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
    $table .= "</table></div>\n";
    return $table;
  }


  function getHTMLHeaderTable() {
    $header = "    <tr>\n      <th>";
    $header .= join("</th><th>", $this->_columns);
    if(!$this->_read_only) $header .= "</th><th>Opérations";
    $header .= "</th>\n    </tr>\n";
    return $header;
  }
  function getHTMLLineTable(&$record, $default_class) {
    $current_class = $default_class;
    $line = "    <tr class=\"$current_class\">\n      <td>";
    $id = $record[0];
    $to_display = array();
    for($i = 0; $i < count($this->_columns); $i++) {
      $to_display []= $record[$i];
    }
    if(!$this->_read_only) {
      $to_display [] = $this->getEditUrl($id);
    }
    $line .= implode("</td><td>", $to_display);
    $line .= "</td>\n    </tr>\n";
    return $line;
  }
  function getFormInput($label, $value) {
    if($label === "id_inspecteur_tiv") {
      $db_query = "SELECT id,nom FROM inspecteur_tiv";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = $result["nom"];
      }
      return $this->constructSelectInputLabels($label, $options, $value);
    } else if($label === "id_bloc") {
      $db_query = "SELECT bloc.id,bloc.id_club,bloc.constructeur,bloc.marque,bloc.capacite,bloc.numero,robinet.serial_number FROM bloc, robinet WHERE robinet.id = bloc.id_robinet";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = "n° ".$result["id_club"]. " (id=".$result["id"].") - ".
                                $result["constructeur"]." (".$result["marque"].") - capacité : ".$result["capacite"]."l - n° série : ".$result["numero"]." - robinet : ".$result["serial_number"];
      }
      return $this->constructSelectInputLabels($label, $options, $value);
    }
    return parent::getFormInput($label, $value);
  }
  function getEditUrl($id) {
    $element_to_manage = "id=$id&element=".$this->_name."&date=".$this->_date."&returnurl=".$this->_back_url;
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";

    return "<a href='edit.php?$element_to_manage' title=\"Éditer cet élément\" class='btn btn-info mr-3'>".
           "<i class='fa fa-pencil-square-o' aria-hidden='rue'></i></a> / ".
           "<a onclick='$delete_confirmation' title='Supprimer cet élément (confirmation nécessaire)' href='delete.php?$element_to_manage' class='btn btn-danger ml-3'>".
           "<i class='fa fa-times' aria-hidden='true'></i></a>";
  }



  function getPossibleStatus($grenaillage = false) {
    $etat_bloc = array("", "Bon", "A suivre", "Mauvais");
    if($grenaillage) $etat_bloc[] = "Grenaillage";
    return $etat_bloc;
  }
}
?>
