<?php
include_once("definition_element.inc.php");
include_once("connect_db.inc.php");
$element = $_POST["element"];

$edit_class = get_element_handler($element, $db_con);
$id = $edit_class->createDBRecord();

if(!$id) {
  $title = "Erreur d'insertion d'un élément $element - club $nom_club";
  include_once('head.inc.php');
  print "Erreur d'insertion du nouvel élément dans la base de données";
  include_once('foot.inc.php');
  exit(1);
}

$show_additional_information = false;
include_once("edit.php");
?>