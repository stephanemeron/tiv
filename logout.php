<?php
include_once('configuration.inc.php');
$title = "Application de gestion du matériel - club $nom_club";
include_once('head.inc.php');

unset($_SESSION["username"]);
unset($_SESSION["password"]);
unset($_SESSION["isAdmin"]);
unset($_SESSION["isSuperAdmin"]);
unset($_SESSION["inLog"]);
unset($_SESSION["isNotUser"]);

echo "<h1 class='text-center'>Vous venez d'être déconnecté</h1>";
header('Refresh: 2; URL = index.php');
