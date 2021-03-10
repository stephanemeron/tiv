<?php
include_once('configuration.inc.php');
if(array_key_exists("element", $_GET)) {
  $element = $_GET['element'];
  $id = $_GET['id'];
} else if(array_key_exists("element", $_POST)) {
  $element = $_POST['element'];
  $id = $_POST['id'];
}
$embedded = array_key_exists("embedded", $_POST) || array_key_exists("embedded", $_GET);

if(!$embedded) {
  $title = "Suppression $element - club $nom_club";
  include_once('head.inc.php');
}

include_once('definition_element.inc.php');
include_once("connect_db.inc.php");
$edit_class = get_element_handler($element, $db_con);

if(!$edit_class->deleteDBRecord($id)) {
  print "<div class='error'>Erreur de suppression de l'élément $element dans la base de données.</div>\n";
} else {
  print "<div class='ok'>Suppression réussi de l'élément $element</div>\n";
  if(!$embedded) {
    print "<script>
setTimeout('window.location.href = \"affichage_element.php?element=$element\"', 1000);
</script>
<p>Vous allez être redirigé automatiquement dans une seconde. Si ce n'est pas le cas, 
cliquer sur le lien suivant : <a href='affichage_element.php?element=$element'>Retour à la liste des ".$element."s</a></p>\n";
  }
}

if(!$embedded) {
  include_once('foot.inc.php');
}
?>