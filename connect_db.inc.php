<?php
if(@is_object($db_con)) return;

$db_con = new mysqli("localhost", "root", "root", "tiv");
//$db_con = new mysqli("mysql5-14.perso", "stephanepcbase", "IhcZCCO0", "stephanepcbase");

if ($db_con->connect_errno) {
    printf("Échec de la connexion : %s\n", $db_con->connect_error);
    exit();
}

$db_con->query("SET NAMES UTF8");
$db_table_prefix = "";
?>
