<?php
include_once('configuration.inc.php');

require_once('connect_db.inc.php');
$title = "Application de gestion du matériel - club $nom_club";
include_once('head.inc.php');

$email = $_POST["email"];
$element = $_POST["element"];
$password = $_POST["password"];

$hashPassword = hash('sha512', $password);
$db_query = "SELECT * FROM personne WHERE personne.email = '$email' AND personne.password = '$hashPassword'" ;
$db_result = $db_con->query($db_query);
if($db_result->num_rows > 0){
    //echo '<pre>'; print_r($db_result->fetch_array()); echo '</pre>';
    while($res = $db_result->fetch_object()){
        settype($res->is_admin, 'integer');
        settype($res->is_super_admin, 'integer');
        if($res->is_admin == 0 && $res->is_super_admin == 0){
            $_SESSION["isNotUser"] = true;
            header('Refresh: 2; URL = signin.php');
        }
        if($res->is_admin == 1){
            $_SESSION["isAdmin"] = true;
            $_SESSION["inLog"] = true;
            unset($_SESSION["isNotUser"]);
        }
        elseif($res->is_super_admin == 1){
            $_SESSION["isAdmin"] = true;
            $_SESSION["isSuperAdmin"] = true;
            $_SESSION["inLog"] = true;
            unset($_SESSION["isNotUser"]);
        }
    }
    header('Refresh: 1; URL = index.php');
}
else{

    header('Refresh: 1; URL = signin.php?unknown=1');
}

include_once('foot.inc.php');
