<h2>Déclaration d'un nouvel élément dans la base</h2>
<p><img src='images/configure.png' style="float: left; margin: 0 15px 0 0;" />
<form name="ajout_form" id="ajout_form" action="ajout_element.php" method="POST">
Type d'élément à déclarer :
<select id="element" name="element">
<option value='bloc'>Bloc</option>
<option value='stab'>Gillet Stabilisateur</option>
<option value='detendeur'>Détendeur</option>
<option value='inspecteur_tiv'>Inspecteur TIV</option>
</select>
<input type="submit" name="submit" onclick='return(confirm("Procéder à la création ?"));' value="Procéder à la création du nouvel élément"/>
</form>
</p>
<h2>Consultation du journal</h2>
<div><img src='images/journal.png' style="float: left; margin: 0 15px 0 0;" />
Consultation des événements du <a href='affichage_element.php?element=journal_tiv'>journal</a>.
</div>
<h2>Consultation d'un TIV</h2>
<p>
<img src='images/consultation-tiv.png' style="float: left; margin: 0 15px 0 0;" />
<form name="consultation_tiv" id="consultation_tiv" action="consultation_tiv.php" method="POST">
Choisissez votre TIV :
<select id="date-tiv-consultation" name="date_tiv" onselect="submit()" >
  <option></option>
<?php
include_once("connect_db.inc.php");
$db_result = $db_con->query("SELECT date, count(id_bloc) FROM inspection_tiv GROUP BY date");
while($result = $db_result->fetch_array()) {
  print "  <option value='".$result["date"]."'>".$result["date"]." (".$result[1]." blocs contrôlé(s))</option>\n";
}
?>
</select>
<input type="submit" name="submit" value="Consulter"/>
</form>
</p>
<h2>Préparation d'un TIV</h2>
<script>
  $(function() {
    $('#choix-tiv').hide();
    $('#choix-tiv-toggle').click(function() {
      $('#choix-tiv').toggle(400);
      return false;
    });
    $.validator.messages.required = "Champ obligatoire";
    $("#preparation_tiv").validate({
      debug: true,
      rules: {
        date_tiv: {
            required: true,
            date: true,
        },
      },
      submitHandler: function(form) {
        if(confirm("Lancer la procédure ?")) form.submit();
      }
    });
  });
</script>
<p><img src='images/creation-seance-tiv.png' style="float: left; margin: 0 15px 0 0;" />
A noter que la préparation d'un TIV consiste à pré-affecter les blocs aux différentes personnes qui feront plus tard le TIV.</p>
<form name="preparation_tiv" id="preparation_tiv" action="preparation_tiv.php" method="POST">
<script>
$(function() {
  $( "#admin-date-tiv-selector" ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    appendText: '(yyyy-mm-dd)',
  });
  $( "#admin-date-tiv-selector" ).datepicker({ altFormat: 'yyyy-mm-dd' });
});
</script>
<p>Date de préparation du TIV :<input type="text" name="date_tiv" id="admin-date-tiv-selector" size="10" value=""/>
<input type="submit" name="lancer" value="Procéder à la pré-affectation" /></p>
<p><a href="#" id="choix-tiv-toggle">Choisir les personnes faisant le TIV</a></p>
<div id="choix-tiv">
<select id="tivs" name="tivs[]" multiple='multiple'>
<?php
include_once("connect_db.inc.php");
$db_result = $db_con->query("SELECT id,nom,actif FROM inspecteur_tiv ORDER BY nom");
while($result = $db_result->fetch_array()) {
  print "  <option value='".$result["id"]."'".($result["actif"] === "oui" ? " selected" : "").">".$result["nom"]."</option>\n";
}
?>
</select>
</div>
</form>
