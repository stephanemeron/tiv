<?php
$title = "Consultation d'une séance de TIV";
include_once('head.inc.php');
include_once('connect_db.inc.php');
include_once('definition_element.inc.php');

if(array_key_exists("date_tiv", $_POST)) {
  $date_tiv = $_POST['date_tiv'];
} else if(array_key_exists("date_tiv", $_GET)) {
  $date_tiv = $_GET['date_tiv'];
} else {
  $date_tiv = $_POST['date_tiv'];
}


$element = "inspection_tiv";
$inspection_tiv = new inspection_tivElement($db_con, $date_tiv);

print "<p>".$inspection_tiv->getParentUrl()."</p>";

?>
<div class="mb-3">
    <h2>Date TIV</h2>
    <div class="d-flex align-items-center"><i class="fa fa-2x fa-calendar mr-3" aria-hidden="true"></i>
    <form name="consultation_tiv" id="consultation_tiv" action="consultation_tiv.php" method="POST">
    <select id="date-tiv-consultation" name="date_tiv" onchange="submit()" >
    <?php
    $db_result = $db_con->query("SELECT date, count(id_bloc) FROM inspection_tiv GROUP BY date");
    while($result = $db_result->fetch_array()) {
        if($result["date"] == $date_tiv){
            $is_selected = ' selected="selected"';
        }
        else{
            $is_selected = '';
        }
      print "  <option value='".$result["date"]."'.$is_selected.>".$result["date"]." (".$result[1]." bloc(s) à contrôler)</option>\n";
    }
    ?>
    </select>
    </form>
    </div>
</div>
<?php
print "<h2>Impression des fiches TIVs</h2>\n";
$pdf_url = "impression_fiche_tiv.php?date=$date_tiv&show_resume=1&show_inspecteur=1&show_all_bloc=1";
print "<div class='d-flex my-3'><a class='btn btn-primary d-flex align-items-self' href='$pdf_url' title='Récupérer les fiches TIV de cette séance au format PDF'>".
      '<i class="fa fa-print fa-2x mr-3" aria-hidden="true"></i> <p class="mb-0">Imprimer les fiches TIV (PDF)</p></a> '.
      " <a class='btn btn-primary d-flex align-items-self ml-md-5' href='$pdf_url&save_as' title='Sauvegarder le PDF des fiches TIV'>".
      '<i class="fa fa-file-pdf-o fa-2x mr-3" aria-hidden="true"></i> <p class="mb-0">Sauvegarder le fichier PDF en local</p></a></div>';

print "<div class='alert alert-info' role='alert'>
            <h4 class='alert-heading'>Informations relatives à l'inspection TIV du $date_tiv</h4>";

$db_query = "SELECT inspection_tiv.id, bloc.date_derniere_epreuve FROM inspection_tiv,bloc ".
            "WHERE date = '$date_tiv' AND bloc.id = inspection_tiv.id_bloc" ;
$db_result = $db_con->query($db_query);
$total = 0;
$count_tiv = 0;
$reepreuve = 0;
$max_time_tiv = strtotime("-48 months", strtotime($date_tiv));
while($result = $db_result->fetch_array()) {
  $total++;
  $time = strtotime($result[1]);
  if($time > $max_time_tiv) $count_tiv++;
  else $reepreuve++;
}

print "Il est prévu d'inspecter $total blocs au total dont $reepreuve réépreuve(s) et ".$count_tiv." inspections TIV.";
print "</div>";
print "<h2>Liste des inspections prévues pour le $date_tiv</h2>\n";

print $inspection_tiv->getHTMLTable("liste-inspection-tiv", $element);

// Inspection de la séance de TIV afin de savoir s'il faut mettre à jour nos blocs.
$db_query = "SELECT count(id_bloc) FROM inspection_tiv,bloc ".
            "WHERE date = '$date_tiv' AND decision = 'OK' AND date_dernier_tiv < '$date_tiv' AND id_bloc = bloc.id";
$db_result = $db_con->query($db_query);
$result = $db_result->fetch_array();
$bloc_to_update = $result[0];

if($bloc_to_update > 0) {
  print "<h2>Valider le TIV ($bloc_to_update bloc(s) à mettre à jour)</h2>
  <form name='update_bloc_tiv' id='update_bloc_tiv' action='update_bloc_tiv.php' method='POST'>
  <input type='hidden' name='date_tiv' value='$date_tiv' />
  <input type='submit' name='lancer' value='Lancer la mise à jour des blocs à partir des fiches de cette journée de TIV'
  onclick='return(confirm(\"Lancer la MAJ des bloc(s) ?\"));' />
  </form>";
} else {
  print "<h2 class='py-5'>Cette inspection n'a pas de bloc à l'état OK ou ne permet pas de mettre à jour les dates d'inspection des blocs</h2>\n";
}

print "<p>".$inspection_tiv->getParentUrl()."</p>";
include_once('foot.inc.php');
?>
