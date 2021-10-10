<?php
class blocElement extends TIVElement {
  var $_current_time;
  var $_epreuve_month_count;
  var $_epreuve_month_count_warn;
  var $_tiv_month_count;
  var $_tiv_month_count_warn;
  var $_robinets;
  var $_field_to_retrieve;
  function blocElement($db_con = false) {

    global $bloc_filetage;
    global $bloc_pression;
    global $bloc_capacite;
    global $array_constructeur;
    global $pression_definition;
    global $bloc_gaz;
    global $bloc_etat;

    parent::__construct($db_con);
    $this->_show_delete_form = true;
    $this->_creation_label = "Création d'un ".$this->_name;
    $this->_update_label = "Mettre à jour le bloc";
    $this->_parent_url       = "/?pills=materiel";
    $this->_parent_url_label = "<i class='fa fa-wrench'></i> Matériel";
    $this->_force_display = array_key_exists("force_bloc_display", $_GET) || array_key_exists("force_bloc_display", $_POST);
    $this->_current_time = time();
    $this->_element_to_link = "nom_proprietaire";
    $this->_elements = array(
      "id" => "ID", "id_club" => "Réf.", "nom_proprietaire" => "Nom propriétaire", "is_club" => "Est au Club", "constructeur" => "Constructeur",
      "marque" => "Marque", "numero" => "N° Série", "capacite" => "Capacité", "filetage" => "Filetage du Bloc", "id_robinet" => "Robinet",
      "date_derniere_epreuve" => "Date dernière épreuve", "date_dernier_tiv" => "Date dernier TIV",
      "pression_service" => "Pression de service", "gaz" => "Gaz", "etat" => "État", "etat_int" => "État intérieur"
    );
    $this->_hidden_column = array("id","constructeur","adresse", "pression_service", "gaz","filetage","is_club","etat", "etat_int");
    $this->_hidden_column_sm = array("id","constructeur", "constructeur", "numero", "marque","capacite","id_robinet", "etat", "etat_int","date_derniere_epreuve","date_dernier_tiv");
    $this->_readonly_column = array("id_club", "constructeur", "marque", "numero","filetage", "capacite", "gaz","pression_service","pression_epreuve");
    $this->_field_to_retrieve = array(
      "robinet" => "CONCAT('Réf: ', serial_number, ' - ', marque, ' - ', nb_sortie,' sortie(s)')");
    //$bloc_capacite = array("6", "10", "12 long", "12 court", "15");
    //$array_constructeur = array("Eurocylinder ECS", "Faber", "IWKA", "Mannesmann", "Roth", "ANCIENS ETABLISSEMENTS POULET (AP)","APOLDAER","CATALINA","CTCO","EM ANZIN","HEISER", "ISER","LUXFER","MCS","MES ALUMINIUM","MILMET SA","OLAER","PRODUCTOS TUBULARES (PT)","SOCIETE DE FORGEAGE DE RIVE-DE-GIER (SFR)","SOCIETE METALLURGIQUE DE GERZAT (SMG)","TENARIS DALMINE (TDL)","VALLOUREC","VITKOVICE","WORTHINGTON");
    // Création d'une dépendance entre pression de service et d'épreuve
    //$pression_definition = array("200" => "300", "230" => "345", "232" => "348", "300" => "450");
    $bloc_pression         = array_keys  ($pression_definition);
    $bloc_pression_epreuve = array_values($pression_definition);
    $this->_form_dependency ["pression_service"] = array("pression_epreuve" => $pression_definition);

    //$bloc_gaz = array("air", "nitrox");
    //$bloc_etat = array("OK", "Rebuté");
    $this->_forms = array(
      "id_club"               => array("required", "number",            "Référence du bloc au sein du club"),
      "nom_proprietaire"      => array("required", false,               "Nom du propriétaire du bloc"),
      "is_club"               => array("required", "boolean",           "Appartient au club"),
      "adresse"               => array(false     , false,               "Adresse du propriétaire du bloc"),
      "constructeur"          => array("required", $array_constructeur, "Constructeur du bloc (ex : ROTH)"),
      "marque"                => array("required", false,               "Marque du bloc (ex : Aqualung)"),
      "numero"                => array("required", false,               "N° de série"),
      "capacite"              => array("required", "radio",             "Capacité du bloc",$bloc_capacite),
      "filetage"              => array("required", "radio",             "Filetage du bloc", $bloc_filetage),
      "id_robinet"            => array(false     , false,               "Référence du robinet"),
      "date_premiere_epreuve" => array("required", "date",              "Date de la première épreuve du bloc"),
      "date_derniere_epreuve" => array("required", "date",              "Date de la dernière épreuve du bloc (tous les 5 ans)"),
      "date_dernier_tiv"      => array("required", "date",              "Date de la dernière inspection visuelle (tous les ans)"),
      "pression_service"      => array("required", "radio",             "Pression de service du bloc (ex : 200 bars)",$bloc_pression),
      "pression_epreuve"      => array("required", "radio",             "Pression épreuve du bloc (ex : 300 bars)",$bloc_pression_epreuve),
      "gaz"                   => array("required", "radio",             "Type de gaz du bloc (air ou nitrox)",$bloc_gaz),
      "etat"                  => array("required", "radio",             "État du bloc",$bloc_etat),
      "etat_int"              => array("required", "number",            "État du bloc intérieur"),
    );
    $this->_form_split_count = 6;
    $this->_forms_rules = '
  "debug": true,
  "rules": {
    "club_id": {
        "required": true
    },
    "nom_proprietaire": {
        "required": true
    },
    "is_club": {
        "required": true
    },
    "adresse": {
        "required": false
    },
    "constructeur": {
        "required": true
    },
    "marque": {
        "required": true
    },
    "numero": {
        "required": true
    },
    "capacite": {
        "required": true
    },
    "filetage": {
        "required": true
    },
    "id_robinet": {
        "required": false
    },
    "date_premiere_epreuve": {
        "required": true,
        "date": true
    },
    "date_derniere_epreuve": {
        "required": true,
        "date": true
    },
    "date_dernier_tiv": {
        "required": true,
        "date": true
    },
    "pression_service": {
        "required": true,
        "number": true
    },
    "pression_epreuve": {
        "required": true,
        "number": true
    },
    "gaz": {
        "required": true
    },
    "etat": {
        "required": true
    },
    "etat_int": {
        "required": true
    }
  }';
    global $epreuve_month_count;
    global $epreuve_month_count_warn;
    global $tiv_month_count;
    global $tiv_month_count_warn;
    $this->_epreuve_month_count = $epreuve_month_count;
    $this->_epreuve_month_count_warn = $epreuve_month_count_warn;
    $this->_tiv_month_count = $tiv_month_count;
    $this->_tiv_month_count_warn = $tiv_month_count_warn;
  }
  function getQuickNavigationFormInput() {
    $input  = " > Navigation rapide : <select name='id' onchange='this.form.submit()'>".
              "<option>Choisir un bloc</option>";
    $db_result = $this->_db_con->query("SELECT id,id_club FROM ".$this->getTableName()." WHERE etat = 'OK' ORDER BY id_club");
    while($result = $db_result->fetch_array()) {
      $selected = ($result['id'] == $_GET['id'] ? " selected" : "");
      $input .= "<option value='".$result['id']."'$selected>n° bloc : ".$result['id_club']."</option>";
    }
    $input .= "</select></p>".
              "</form>";
    return $input;
  }
  function getEpreuveWarnMonthCount() {
    return $this->_epreuve_month_count - $this->_epreuve_month_count_warn;
  }
  function getTIVWarnMonthCount() {
    return $this->_tiv_month_count - $this->_tiv_month_count_warn;
  }

  function getHTMLLineTable(&$record, $default_class) {
    $current_class = $default_class;
    if($tmp = $this->updateRecord($record)) {
      $current_class = $tmp;
    }
    $line = "    <tr class=\"$current_class\"> ";
    $id = $record[0];
    $to_display = array();


    foreach($this->getElements() as $elt) {
      if(preg_match("/id_(.*)/", $elt, $tmp)) {
        if(array_key_exists($tmp[1],$this->_field_to_retrieve)){
          $db_query = "SELECT id,".$this->_field_to_retrieve[$tmp[1]]." FROM ".$tmp[1]." WHERE id=".$id;
          $db_result = $this->_db_con->query($db_query);

          $result = $db_result->fetch_array();
          $record[$elt] = '<a href="/edit.php?id='.$id.'&element='.$tmp[1].'">'.$result[1].'</a>';
        }
      }

      if (in_array($elt, $this->_hidden_column)){
        $to_display []= array("d-none",$record[$elt]);
      }
      else{
        if (in_array($elt, $this->_hidden_column_sm)){
          $to_display []= array("d-none d-md-table-cell",$record[$elt]);
        }
        else{
          $to_display []= $record[$elt];
        }
      }

    }

    if(!$this->_read_only) {
      $to_display [] = $this->getEditUrl($id);
    }
    //echo'<pre>'; print_r($to_display);echo'</pre>';

    $positionKeyToLink = array_search($this->_element_to_link, array_keys($this->_elements));
    foreach ($to_display as $key => $value) {
      if (is_array($value)){
        if($key == $positionKeyToLink){
            $line .= '<td class="'.$value[0].'">';
                $line .= '<a class="link" href="edit.php?'.$this->getURLReference($id).'" title="Éditer cet élément">';
                    $line .= $value[1];
                $line .= "</a>";
            $line .= '</td>';
        }
        else{
            $line .= '<td class="'.$value[0].'">';
                $line .= $value[1];
            $line .= '</td>';
        }
      }
      else{
        if($key == $positionKeyToLink){
            $line .= "<td>";
                $line .= '<a class="link" href="edit.php?'.$this->getURLReference($id).'" title="Éditer cet élément">';
                    $line .= $value;
                $line .= "</a>";
            $line .= "</td>";
        }
        else{
            $line .= '<td>'.$value.'</td>';
        }
      }
    }
    //$line .= implode("</td><td>", $to_display);
    //$line .= "</td>    </tr>";
    $line .= "</tr>";
    return $line;
  }



  function getFormInput($label, $value) {
    if($label === "id_robinet") {
      $db_query = "SELECT id,serial_number,marque,nb_sortie, filetage FROM robinet";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = $result["serial_number"]."-".$result["marque"]."-".$result["nb_sortie"]." sortie(s)";
      }
      return $this->constructSelectInputLabels($label, $options, $value);
    }
    return parent::getFormInput($label, $value);
  }


  function constructResume($table_label, $time, $column, $div_label_to_update, $error_label, $error_class, $label_ok) {
    $db_query = "SELECT ".join(",", $this->getElements())." FROM bloc ".
                "WHERE $column < '".date("Y-m-d", $time)."'";
    $table_code = $this->getHTMLTable($table_label, $this->_name, $db_query, false);
    $message_alerte = $label_ok;
    if($this->_record_count > 0) {
      $message_alerte = str_replace("__COUNT__", $this->_record_count, $error_label);
    } else {
      $error_class = 'ok d-flex align-items-center';
    }
    $html_code = "<p><div class='$error_class'>$message_alerte</div></p>";
    $html_code .= "<script>
$('#$div_label_to_update').html(\"$message_alerte\");
document.getElementById('$div_label_to_update').className='$error_class';
</script>";
    return $html_code.$table_code;
  }
  function getAdditionalControl() {
    $additional_element = parent::getAdditionalControl();
    if(!array_key_exists("force_bloc_display", $_GET))
      return "<p>$additional_element</p><p><a href='affichage_element.php?element=bloc&force_bloc_display=1'>".
             "Forcer l'affichage de tous les blocs (y compris rebuté)</a></p>";
    else
      return "<p>$additional_element</p>";
  }
  function isDisplayed(&$record) {
    return ($record["etat"] == "OK");
  }
  function updateRecord(&$record) {
    // Test présence champ nécessaire aux tests à réaliser
    foreach(array("date_dernier_tiv", "date_derniere_epreuve", "etat") as $elt) {
      if(!array_key_exists($elt, $record)) { return false; }
    }
    if($record["etat"] != "OK") {
      $record["etat"] = "<div class='bg-danger critical'>".$record["etat"]."</div>";
      return "critical-etat";
    }
    foreach(array("date_dernier_tiv","date_derniere_epreuve") as $field) {

        if($tmp = $this->getDateDivClass($field, $record[$field], $comment, $next_date, $class_status)) {
          $record[$field] = "<div class='$class_status $tmp'>".$record[$field]."</div>";
        }

    }
    if($tmp != "ok"){
      return $tmp."-epreuve";
    }

  }
  function getTIVForm($id) {
    $form = "<script>
  $(function() {
    $.validator.messages.required = 'Champ obligatoire';
    $('#preparation_tiv').validate({
      debug: false,
      rules: {
        date_tiv: {
            required: true,
            date: true,
        },
      },
      submitHandler: function(form) {
        form.submit();
      }
    });
  });
</script>
<h3>Création d'une fiche TIV individuelle</h3>
<form name='preparation_tiv' id='preparation_tiv' action='preparation_tiv.php' method='POST'>
  <input type='hidden' name='id_bloc' value='$id'/>
  <script>
  $(function() {
    $( '#admin-date-tiv-selector' ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd',
      appendText: '(dd-mm-yyyy)',
      language: 'fr',
      altFormat: 'dd-mm-yyyy'
    });
  });
  </script>
  <p>Date de l'inspection TIV :<input type='text' name='date_tiv' id='admin-date-tiv-selector' size='10' value=''/>
  - Nom de l'inspecteur TIV : <select id='tivs' name='tivs[]'>
    <option></option>";
      $db_result = $this->_db_con->query("SELECT id,nom,actif FROM inspecteur_tiv WHERE actif = 'oui' ORDER BY nom");
      while($result = $db_result->fetch_array()) {
        $form .= "  <option value='".$result["id"]."'>".$result["nom"]."</option>";
      }
      $form .= "</select>
  <input type='submit' name='lancer' value='Créer la fiche TIV' class='btn btn-outline-primary' /></p>
</form>";
    return $form;
  }
  function getDateDivClass($label, $value, &$comment, &$next_date, &$class_status) {
    $status = false;
    $time_value = strtotime($value);
    $comment = false;
    $next_date = false;
    $class_status = false;
    if($time_value){
      if($label == "date_derniere_epreuve") {
        $status             = "ok";
        $class_status       = "text-success";
        $comment            = "Prochaine ré-épreuve : ";
        $next_epreuve       = strtotime("+".$this->_epreuve_month_count." months",      $time_value);
        $next_epreuve_warn  = strtotime("+".$this->_epreuve_month_count_warn." months", $time_value);
        $next_date = date("Y-m-d", $next_epreuve);
        if($next_epreuve < $this->_current_time and $next_epreuve != "") {
          $status       = "critical";
          $class_status = "text-danger";
          $comment      = "DATE DE RÉ-ÉPREUVE DÉPASSÉE !!! ";
        } else if($next_epreuve_warn < $this->_current_time) {
          $comment      = "Attention !!! Date de ré-épreuve bientôt dépassé ! ";
          $status       = "warning";
          $class_status = "text-warning";
        }
      } else if($label == "date_dernier_tiv") {
        $comment        = "Prochaine inspection TIV : ";
        $status         = "ok";
        $class_status   = "text-success";
        $next_tiv       = strtotime("+".$this->_tiv_month_count." months",      $time_value);
        $next_tiv_warn  =strtotime("+".$this->_tiv_month_count_warn." months", $time_value);
        $next_date      = date("Y-m-d", $next_tiv);
        if($next_tiv < $this->_current_time) {
          $status       = "critical";
          $class_status = "text-danger";
          $comment      = "DATE DE TIV DÉPASSÉE !!! : ";
        } else if($next_tiv_warn < $this->_current_time) {
          $comment      = "Attention !!! Date de TIV bientôt dépassé ! ";
          $status       = "warning";
          $class_status = "text-warning";
        }
      }
    }
    return $status;
  }
  function getElementLabel($label, $value) {
    $text = parent::getElementLabel($label, $value);
    if($tmp = $this->getDateDivClass($label, $value, $comment, $next_date, $class_status)) {
      $text .= "<div class='$class_status $tmp'>$comment$next_date</div>";
    }
    return $text;
  }
  function getExtraInformation($id) {
    // Recherche d'info sur les dates d'epreuves et dernière inspection
    $db_result = $this->_db_con->query("SELECT date_derniere_epreuve,date_dernier_tiv,etat FROM bloc WHERE id = $id");
    $result = $db_result->fetch_array();
    $bg_color = "info";
    if($result["etat"] != "OK") {
      //return "Bloc rebuté.";
      return array('bg_color' => "danger", 'extra_info' => "Bloc rebuté.");
    }

    // Construction des timestamps pour calcul date
    $derniere_epreuve = strtotime($result[0]);
    $dernier_tiv = strtotime($result[1]);
    // Construction des dates d'expiration
    $next_epreuve = strtotime("+".$this->_epreuve_month_count." months", $derniere_epreuve);
    $next_epreuve_minus_one = strtotime("+".$this->_epreuve_month_count_warn." months", $derniere_epreuve);
    $next_tiv = strtotime("+".$this->_tiv_month_count." months", $dernier_tiv);
    $next_tiv_minus_one = strtotime("+".$this->_tiv_month_count_warn." months", $dernier_tiv);
    $message_expiration  = "<div><i class='fa fa-calendar-check-o fa-2x text-success mx-3' aria-hidden='true'/></i> ".
                           "Date prochaine réépreuve : <strong>".date("d/m/Y", $next_epreuve)."</strong> - ".
                           "Date prochain TIV : <strong>".date("d/m/Y", $next_tiv)."</strong></div>";
    if($next_epreuve < $this->_current_time) {
      $bg_color = "danger";
      $message_expiration = "<div class='error'><i class='fa fa-calendar-times-o fa-2x text-danger mx-3' aria-hidden='true'/></i> ".
                            "ATTENTION !!! CE BLOC A DÉPASSÉ SA DATE DE RÉÉPREUVE (le ".date("d/m/Y", $next_epreuve).") !!!</div>";
    } else if($next_epreuve_minus_one < $this->_current_time) {
      $bg_color = "warning";
      $message_expiration = "<div class='warning'><i class='fa fa-calendar fa-2x text-warning mx-3' aria-hidden='true'/></i> ".
                            "Attention, ce bloc va bientôt dépasser sa date de réépreuve ".
                            "(dans moins de ".$this->getEpreuveWarnMonthCount()." mois, le ".date("d/m/Y", $next_epreuve).")</div>";
    }
    if($next_tiv < $this->_current_time) {
      $bg_color = "danger";
      $message_expiration = "<div class='error'><i class='fa fa-calendar-times-o fa-2x text-danger mx-3' aria-hidden='true'/></i> ".
                            "Attention !!! ce bloc a dépassé sa date de TIV (le ".date("d/m/Y", $next_tiv).")</div>";
    } else if($next_tiv_minus_one < $this->_current_time) {
      $bg_color = "warning";
      $message_expiration = "<div class='warning'><i class='fa fa-calendar fa-2x text-warning mx-3' aria-hidden='true'/></i> ".
                            "Attention, ce bloc va bientôt dépasser sa date de TIV ".
                            "(dans moins de ".$this->getTIVWarnMonthCount()." mois, le ".date("d/m/Y", $next_tiv).")</div>";
    }
    // Récupération d'information sur les fiches TIV du bloc
    $db_result = $this->_db_con->query("SELECT id,date FROM inspection_tiv WHERE id_bloc = $id ORDER BY date DESC");
    $extra_info = array();
    while($result = $db_result->fetch_array()) {
      $extra_info []= "<a class='btn btn-info mr-3' href='edit.php?id=".$result[0]."&element=inspection_tiv&date=".$result[1]."' ".
                      "title='Éditer la fiche TIV'><i class='fa fa-eye' aria-hidden='true'/></i> ".
                      "Inspection TIV du ".$result[1]."</a>".
                      "<a class='btn btn-info' href='impression_fiche_tiv.php?id_bloc=$id&date=".$result[1]."' title='Accéder à la fiche au format PDF'>".
                      "<i class='fa fa-file-pdf-o' aria-hidden='true'/></i>  fiche PDF</a>";
    }
    // Composition des messages
    $message = "";
    $message = "<p>$message_expiration</p>";
    if(count($extra_info) > 0) {
      $message .= "<h3>Liste des fiches d'inspection TIV associées au bloc :</h3><dl><dd>".implode("</dd><dd>", $extra_info)."</dd></dl>";
    } else {
      $message .= "<p>Pas de fiche d'inspection TIV associée au bloc.</p>";
    }
    $message .= $this->getTIVForm($id);
    //return $message;
    return array('bg_color' => $bg_color, 'extra_info' => $message);
  }
}
?>
