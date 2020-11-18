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
<div class="container-fluid">
  <div class"row">
    <div id="MenuNavigation" class="col">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" id="accueil-tab" data-toggle="tab" href="#accueil" role="tab" aria-controls="accueil" aria-selected="true" title="Accueil gestion matériel" ><i class='fa fa-home'></i> Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab" aria-controls="admin" aria-selected="true" title="Administration du site"><i class='fa fa-sliders'></i> Administration</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="materiel-tab" data-toggle="tab" href="#materiel" role="tab" aria-controls="materiel" aria-selected="true" title="Liste du matériel du club" ><i class='fa fa-wrench'></i> Matériel</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="personne-tab" data-toggle="tab" href="#personne" role="tab" aria-controls="personne" aria-selected="true" title="Liste des personnes/TIV du club"><i class='fa fa-graduation-cap'></i> Personnes/inspecteurs TIV</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="bloc-rev-tab" data-toggle="tab" href="#bloc-rev" role="tab" aria-controls="bloc-rev" aria-selected="true" title="Prochaine révisions des blocs"><i class='fa fa-fire-extinguisher'></i> Status des blocs (TIV/ré-épreuve)</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" role="tabpanel" aria-labelledby="accueil-tab" id="accueil">
          <?php include("accueil.php");?>
        </div>
        <div class="tab-pane" role="tabpanel" aria-labelledby="admin-tab" id="admin">
          <?php include("admin.php");?>
        </div>
        <div class="tab-pane" role="tabpanel" aria-labelledby="materiel-tab" id="materiel">
          <?php include("materiel.php");?>
        </div>
        <div class="tab-pane" role="tabpanel" aria-labelledby="personne-tab" id="personne">
          <?php include("personne.php");?>
        </div>
        <div class="tab-pane" role="tabpanel" aria-labelledby="bloc-rev-tab" id="bloc-rev">
          <?php include("affichage_bloc_tiv.php"); ?>
        </div>
      </div>
    </div>
<?php
include_once('foot.inc.php');
?>