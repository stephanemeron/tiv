<?php
$date_tiv = $_GET["date"];
require_once("gestion_impression.inc.php");
require_once("connect_db.inc.php");

$pdf = new PdfTIV($date_tiv, $db_con);

// Affiche un résumé du PDF
if(array_key_exists("show_resume", $_GET))
  $pdf->addResume();
// Affiche les fiches résumant les blocs inspectés par chacun
if(array_key_exists("show_inspecteur", $_GET))
  $pdf->addInspecteurFile();
// Affiche les fiches des blocs à inspecter
if(array_key_exists("show_all_bloc", $_GET))
  $pdf->addBlocFile();
// Affiche la fiche d'un bloc
if(array_key_exists("id_bloc", $_GET))
  $pdf->addBlocFile($_GET["id_bloc"]);

if(array_key_exists("save_as", $_GET)) {
  $pdf->Output("Séance_TIV_du_$date_tiv.pdf", "D");
} else {
  $pdf->Output();
}
?>