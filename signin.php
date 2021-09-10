<?php
    include_once('configuration.inc.php');
    $title = "Application de gestion du matériel - club $nom_club";
    include_once('head.inc.php');

if($_SESSION["isNotUser"]){
    echo "<h1 class='text-center'>Vous n'êtes pas autorisés à vous connecter à ce site</h1>";
}
?>
    <form class="form-signin" action="login.php" method="post">
      <div class="text-center">
          <img class="mb-4 mx-auto" src="<?php print $logo_club; ?>" alt="" width="144" height="144">
      </div>
      <h1 class="h3 mb-3 font-weight-normal">Identifiez vous</h1>
      <?php
          if(isset($_GET) && $_GET['unknown']){
              echo '<div class="alert alert-danger" role="alert">
                      <h2 class="h3 text-error"> Connexion Impossible</h2>
                      <p>Adresse mail ou mot de passe incorrect ou inconnu.</p>
                      <p>Rapprochez vous du bureau de Captaine Némo pour plus de renseignemets</p>
                    </div>';
          }
      ?>
      <label for="inputEmail" class="sr-only">Email (Doit être unique pour se connecter à se service)</label>
      <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required="" autofocus="">
      <label for="inputPassword" class="sr-only">Mot de passe</label>
      <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="">
      <input type="hidden" name="element" id="element" value="personne">

      <button class="btn btn-lg btn-primary btn-block" type="submit">Connexion</button>
    </form>
    <form class="form-signin" action="logout.php">
        <div class="text-center">
            <p>Cliquez ci-dessous pour quitter </p>
            <button href = "logout.php" class="btn btn-lg btn-alert btn-block" tite = "Logout" type="submit">votre session</button>
        </div>
    </form>

<?php
    include_once('foot.inc.php');
?>
