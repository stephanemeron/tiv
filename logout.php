<?php
include_once('configuration.inc.php');
$title = "Application de gestion du matériel - club $nom_club";
include_once('head.inc.php');

unset($_SESSION["username"]);
unset($_SESSION["password"]);

echo "Vous venez d'être déconnecté";
header('Refresh: 2; URL = index.php');
