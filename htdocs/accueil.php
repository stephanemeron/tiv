<div class="jumbotron my-3">
	<div class="row">
		<div class="col-12 col-md-3">
			<img class="img-fluid" src='<?php print $logo_club; ?>' />
		</div>
		
		<div class="col-12 col-md-9 pt-3 pt-md-0">
			<h1 class="title">Bienvenue sur le site de gestion du matériel du club <?php print $nom_club; ?></h1>
			<p>
			Vous êtes à la racine du site permettant de gérer le matériel du club <?php print $nom_club; ?>.
			</p>
			<ul>
				<li>L'onglet bloc/détendeur/stabs vous donnera un recensement simple du matériel du club.</li>
				<li>L'onglet Inspecteur TIV vous donnera la liste des personnes recensées dans le club en mesure de faire des inspections visuelles. Vous pourrez également accéder à la liste des blocs qui auront été inspectées par chaque TIV.</li>
				<li>L'onglet Status des blocs (TIV/ré-épreuve) vous donnera une liste des blocs nécessitant des blocs dans moins de 5 mois ainsi que les blocs nécessitant une inspection TIV dans moins de 1 mois.</li>
			</ul>
			<p>Bonne inspection de bloc !</p>
		</div>
	</div>
	
</div>
<div class="jumbotron my-3">
	<h2 class="title">Messages importants</h2>
	<div class="bg-warning" id="message_important_reepreuve">--</div>
	<div class="bg-danger" id="message_important_tiv">++</div>
</div>
