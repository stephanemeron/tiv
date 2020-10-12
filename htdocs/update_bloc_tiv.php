<?php
$date_tiv = $_POST["date_tiv"];

require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

$title = "Mise à jour des blocs du club suite à l'inspection TIV du $date_tiv";
include_once('head.inc.php');

$decision = array();
print "<h2>Mise à jour de(s) bloc(s) suite à l'inspection TIV du $date_tiv</h2>\n";
if(array_key_exists("blocs_to_update", $_POST)) {
  $blocs_to_update = $_POST["blocs_to_update"];
  if(!is_array($blocs_to_update)) $blocs_to_update = array($blocs_to_update);
  foreach($blocs_to_update as $bloc_id) {
    $db_query = "SELECT decision FROM inspection_tiv,bloc WHERE inspection_tiv.date='$date_tiv' ".
                "AND id_bloc=bloc.id AND inspection_tiv.id_bloc = $bloc_id";
    $db_result = $db_con->query($db_query);
    while($result = $db_result->fetch_array()) {
      $decision[$bloc_id] = $result[0];
    }
  }
} else {
  $blocs_to_update = array();
  $db_query = "SELECT id_bloc,decision FROM inspection_tiv,bloc ".
              "WHERE date = '$date_tiv' AND date_dernier_tiv < '$date_tiv' AND id_bloc = bloc.id AND decision != ''";

  $db_result = $db_con->query($db_query);
  while($result = $db_result->fetch_array()) {
    $blocs_to_update []= $result[0];
    $decision[$result[0]] = $result[1];
  }
}

foreach($blocs_to_update as $bloc_id) {
  $state = $decision[$bloc_id];
  if($state != "OK") {
    print "<div class='warning'>Passage à l'état $state</div>\n";
  }
  add_journal_entry($db_con, $bloc_id, 'bloc', "Passage du bloc à l'état '$state' et date TIV au '$date_tiv'");
  $db_query = "UPDATE bloc SET date_dernier_tiv = '$date_tiv', etat = '$state' WHERE id = '$bloc_id'";
  if(!$db_con->query($db_query)) {
    print "<div class='error'>Erreur de mise à jour du bloc '$bloc_id'</div>\n";
  } else {
    print "<div class='ok'>Mise à jour du bloc '$bloc_id' OK</div>\n";
  }
}
print "<p><a href='consultation_tiv.php?date_tiv=$date_tiv'>Revenir dans la liste des inspections TIV du $date_tiv</a></p>\n";

include_once('foot.inc.php');
?>