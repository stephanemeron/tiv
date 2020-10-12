<?php
$title = "Préparation d'une séance de TIV";
include_once('head.inc.php');
include_once('connect_db.inc.php');

$tivs = $_POST["tivs"];
$date_tiv = $_POST["date_tiv"];
$bloc_condition = "1";
if(array_key_exists("id_bloc", $_POST)) {
  $bloc_condition = "id = ".$_POST["id_bloc"];
}

$current_tiv = 0;
$db_result = $db_con->query("SELECT id, date_derniere_epreuve FROM bloc WHERE $bloc_condition AND etat = 'OK' ORDER BY capacite,constructeur");
while($bloc = $db_result->fetch_array()) {
  $tiv = $tivs[$current_tiv++%count($tivs)];
  $request = "INSERT INTO inspection_tiv (id, id_bloc, id_inspecteur_tiv, date) VALUES ".
             "(0, ".$bloc["id"].", $tiv, '$date_tiv')";
  if(!$db_con->query($request)) {
    print "Erreur lors de la preparation des TIVs.";
    print "<pre>$request</pre>";
  }
}

include_once('consultation_tiv.php');
include_once('foot.inc.php');
?>