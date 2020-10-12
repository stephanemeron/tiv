<?php
include_once("definition_element.inc.php");
include_once("connect_db.inc.php");

$id = $_POST["id"];
$element = $_POST["element"];

$edit_class = get_element_handler($element, $db_con);

if(array_key_exists("delete", $_POST)) {
  include("delete.php");
  exit();
}

$result = $edit_class->updateDBRecord($id, $_POST);

if($result == 2) {
  print "<div class='ok'>Pas de mise à jour</div>";
} else if($result == 1) {
  print "<div class='ok'>Mise à jour OK</div>";
} else {
  print "<div class='error'>Problème lors de la mise à jour</div>";
}
?>