<?php
include_once('configuration.inc.php');
$title = "Application de gestion du matériel - club $nom_club";
include_once('head.inc.php');
?>
<script>
  $(function() {
    $( "#MenuNavigation" ).tabs();
  });
</script>
<div id="MenuNavigation">
  <ul>
    <li><a href="#accueil"        title="Accueil gestion matériel"       ><img src='images/accueil.png' /> Accueil</a></li>
    <li><a href="#admin"          title="Administration du site"         ><img src='images/admin.png' /> Administration</a></li>
    <li><a href="#materiel"       title="Liste du matériel du club"      ><img src='images/materiel.png' /> Matériel</a></li>
    <li><a href="#personne"       title="Liste des personnes/TIV du club"><img src='images/personne.png' /> Personnes/inspecteurs TIV</a></li>
    <li><a href="#bloc-rev"       title="Prochaine révisions des blocs"  ><img src='images/etat-bloc.png' /> Status des blocs (TIV/ré-épreuve)</a></li>
  </ul>
  <div id="accueil">
    <?php include("accueil.php");?>
  </div>
  <div id="admin">
    <?php include("admin.php");?>
  </div>
  <div id="materiel">
    <?php include("materiel.php");?>
  </div>
  <div id="personne">
    <?php include("personne.php");?>
  </div>
  <div id="bloc-rev">
    <?php include("affichage_bloc_tiv.php"); ?>
  </div>
</div>
<?php
include_once('foot.inc.php');
?>