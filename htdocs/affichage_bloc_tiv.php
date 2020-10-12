<?php
require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

$message_alerte_reepreuve = "Rien à signaler concernant les réépreuves";
$message_alerte_tiv       = "Rien à signaler concernant les inspections TIV";

$bloc_element = new blocElement($db_con);

$epreuve_month_interval = $epreuve_month_count - $epreuve_month_count_warn;
$tiv_month_interval = $tiv_month_count - $tiv_month_count_warn;

print $bloc_element->constructResume("blocs-reepreuve", strtotime("-$epreuve_month_count_warn month"),
                                     "date_derniere_epreuve", "message_important_reepreuve",
                                     "<img src='images/security-low.png' style='vertical-align:bottom;' /> ".
                                     "__COUNT__ bloc(s) nécessite une ré-épreuve dans moins de $epreuve_month_interval mois.", 'error',
                                     "<img src='images/security-high.png' style='vertical-align:bottom;' /> Rien à signaler concernant les réépreuves");

print $bloc_element->constructResume("blocs-tiv", strtotime("-$tiv_month_count_warn month"),
                                     "date_dernier_tiv", "message_important_tiv",
                                     "<img src='images/security-low.png' style='vertical-align:bottom;' /> ".
                                     "__COUNT__ bloc(s) nécessite une inspection TIV dans moins de $tiv_month_interval mois.", 'warning',
                                     "<img src='images/security-high.png' style='vertical-align:bottom;' /> Rien à signaler concernant les inspections TIV");

?>