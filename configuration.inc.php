<?php
$nom_club     = "Némo";
$numero_club  = "XX.XX.XXXX";
$adresse_club = "Adresse du club\nCode postal Ville";
$logo_club    = "/images/nemo.png";
$email_club   = "email@club.com";

// Données bloc
$bloc_capacite = array("6", "10", "12 long", "12 court", "15");
$array_constructeur = array("Eurocylinder ECS", "Faber", "IWKA", "Mannesmann", "Roth", "ANCIENS ETABLISSEMENTS POULET (AP)","APOLDAER","CATALINA","CTCO","EM ANZIN","HEISER", "ISER","LUXFER","MCS","MES ALUMINIUM","MILMET SA","OLAER","PRODUCTOS TUBULARES (PT)","SOCIETE DE FORGEAGE DE RIVE-DE-GIER (SFR)","SOCIETE METALLURGIQUE DE GERZAT (SMG)","TENARIS DALMINE (TDL)","VALLOUREC","VITKOVICE","WORTHINGTON");
// Création d'une dépendance entre pression de service et d'épreuve
$pression_definition = array("200" => "300", "230" => "345", "232" => "348", "300" => "450");
$bloc_gaz = array("air", "nitrox");
$bloc_etat = array("OK", "Rebuté");

// Données bloc et robinet
$bloc_filetage = array("M 25 x 2 ISO", "25 x 200 SI", "G 3/4 DIN 259", "E17 Conique", "M18X1.5 ISO");

// Données robinet
$robinet_filetage_sortie = array("G5/8 ISO 228-1", "M26x2 (Nitrox)");
$spec_robinet = array("SS", "DSA", "DSS");

$epreuve_month_count = 60;
$epreuve_month_count_warn = 55;
$tiv_month_count = 12;
$tiv_month_count_warn = 11;
?>
