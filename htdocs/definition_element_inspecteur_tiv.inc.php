<?php
class inspecteur_tivElement extends TIVElement {
  function inspecteur_tivElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#personne";
    $this->_parent_url_label = "<img src='images/personne.png' /> Plongeurs/inspecteurs TIV";
    $this->_creation_label = "Création d'un nouvel inspecteur";
    $this->_update_label = "Mettre à jour l&#145;inspecteur TIV";
    $this->_elements = array("id" => "Réf.", "nom" => "Prénom Nom", "numero_tiv" => "Numéro d'inspecteur TIV",
                             "adresse_tiv" => "Adresse du TIV", "telephone_tiv" => "Téléphone de l'inspecteur", "actif" => "Actif ?",);
    $this->_forms = array(
      "nom"           => array("required", "text", "Nom de l'inspecteur TIV"),
      "numero_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
      "adresse_tiv"   => array("required", "text", "Adresse du TIV"),
      "telephone_tiv" => array("required", "text", "Téléphone du TIV"),
      "actif"         => array("required", array("oui", "non"), "Le TIV est-il actif ?"),
    );
    $this->_forms_rules = '
  debug: false,
  rules: {
    nom: {
        required: true,
    },
    numero_tiv: {
        required: true,
    },
    adresse_tiv: {
        required: true,
    },
    telephone_tiv: {
        required: true,
    },
    actif: {
        required: true,
    },
  }';
  }
  function getQuickNavigationFormInput() {
    $input  = " > Navigation rapide<select name='id' onchange='this.form.submit()'>\n".
              "<option></option>\n";
    $db_result = $this->_db_con->query("SELECT id,nom FROM ".$this->getTableName()." ORDER BY nom");
    while($result = $db_result->fetch_array()) {
      $selected = ($result['id'] == $_GET['id'] ? " selected" : "");
      $input .= "<option value='".$result['id']."'$selected>".$result['nom']." (id inspecteur TIV n° ".$result['id'].")</option>\n";
    }
    $input .= "</select></p>".
              "</form>";
    return $input;
  }
  function getExtraInformation($id) {
    $db_query = "SELECT inspection_tiv.id,date,id_club,decision,id_bloc ".
                "FROM inspection_tiv,bloc ".
                "WHERE id_inspecteur_tiv = $id AND id_bloc = bloc.id ORDER BY date, id_club";
    $db_result = $this->_db_con->query($db_query);
    $extra_info = array();
    $count = 0;
    while($result = $db_result->fetch_array()) {
      $extra_info []= "<td>".$result[1]."</td>".
                      "<td>bloc n° ".$result[2]."</a></td>".
                      "<td>".$result[3]."</a></td>".
                      "<td><a href='edit.php?id=".$result[0]."&element=inspection_tiv&date=".$result[1]."'>".
                      "<img src='images/edit.png' style='vertical-align:middle;' /> Fiche TIV n° ".$result[0]."</a>".
                      "/<a href='impression_fiche_tiv.php?id_bloc=".$result[4]."&date=".$result[1]."'>".
                      "<img src='images/pdf.png' style='vertical-align:middle;' /> fiche PDF</a></td>";
      $count++;
    }
    if($count > 0) {
      return "<h3>Liste des fiches d'inspection TIV associées à l'inspecteur :</h3>\n".
             $this->getJSOptions("liste-inspections", "fiche", 10).
"<table cellpadding='0' cellspacing='0' border='0' class='display' id='liste-inspections'>
<thead><tr><th>Date inspection TIV</th><th>Références bloc</th><th>Décision</th><th>Éditer la fiche / Accéder au PDF</th></tr></thead>
<tbody>
<tr>".implode("</tr>\n<tr>", $extra_info)."</tr>
</tbody></table>\n";
    } else {
      return "<div class='ok'>Pas de fiche TIV associées avec cet utilisateur.</div>";
    }
  }
}
?>