<?php
$nom_club     = "Némo";
$numero_club  = "XX.XX.XXXX";
$adresse_club = "Adresse du club\nCode postal Ville";
$logo_club    = "/images/nemo.png";
$favicon    = "/images/favicon.ico";
$email_club   = "email@club.com";

// Params pour les durées de Validité pour le TIV
$epreuve_month_count = 60;
$epreuve_month_count_warn = 55;
$tiv_month_count = 12;
$tiv_month_count_warn = 11;

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

// personneElement$qualifications_label = array("", "Nitrox", "Nitrox confirmé", "BIO AFBS", "BIO IFBS",
$qualifications_label = array("", "Nitrox", "Nitrox confirmé", "BIO AFBS", "BIO IFBS",
                              "Bio 1", "Bio 2", "E1", "E2", "MFB1", "MFB2", "TIV", "RIFAP");
$niveau = array("", "Débutant", "Niveau 1", "Niveau 2", "Niveau 2 Initiateur", "Niveau 3", "Niveau 3 Initiateur", "Niveau 4", "Niveau 4 Initiateur", "Moniteur MF1", "Moniteur MF2");
$assurance = array("", "1", "2", "3");

// Stabs
$stab_taille = array("junior", "XS", "S", "M", "M/L", "L", "XL", "XXL");

// Prêts
$etat_possible = array("", "Sortie", "Rentré");
?>
