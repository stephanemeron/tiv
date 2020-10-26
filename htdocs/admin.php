<div class="my-5">
  <h2 class="title">Déclaration d'un nouvel élément dans la base</h2>
  
  <form name="ajout_form" id="ajout_form" action="ajout_element.php" method="POST">
    <div class="row">
      <div class="col-auto">
        <p class=""><i class="fa fa-cogs" aria-hidden="true"></i> Type d'élément à déclarer :</p>
      </div>
      <div class="col-12 col-md-3">
        <select class="custom-select" id="element" name="element">
          <option value='bloc'>Bloc</option>
          <option value='robinet'>Robinet</option>
          <option value='stab'>Gillet Stabilisateur</option>
          <option value='detendeur'>Détendeur</option>
          <option value='inspecteur_tiv'>Inspecteur TIV</option>
        </select>
      </div>
      <div class="col-12 col-md-3 mt-4 mt-md-0">
        <input type="submit" class="btn btn-secondary" name="submit" onclick='return(confirm("Procéder à la création ?"));' value="Procéder à la création du nouvel élément"/>
      </div>
    </div>
  </form>
</div>

<div class="my-5">
  <h2 class="title">Consultation du journal</h2>
  <div><p class=""><i class="fa fa-newspaper-o" aria-hidden="true"></i> Consultation des événements du <a class="btn btn-secondary" href='affichage_element.php?element=journal_tiv'>journal</a>.</p>
  </div>
</div>

<div class="my-5">
  <h2 class="title">Consultation d'un TIV</h2>
  <form name="consultation_tiv" id="consultation_tiv" action="consultation_tiv.php" method="POST">
    <div class="row">
      <div class="col-auto">
        <p class=""><i class="fa fa-book" aria-hidden="true"></i> Choisissez votre TIV :</p>
      </div>
      <div class="col-12 col-md-3">
        <select class="custom-select" id="date-tiv-consultation" name="date_tiv" onselect="submit()">
          <option></option>
          <?php
            include_once("connect_db.inc.php");
            $db_result = $db_con->query("SELECT date, count(id_bloc) FROM inspection_tiv GROUP BY date");
            while($result = $db_result->fetch_array()) {
              print "  <option value='".$result["date"]."'>".$result["date"]." (".$result[1]." blocs contrôlé(s))</option>\n";
            }
          ?>
        </select>
      </div>
      <div class="col-12 col-md-3 mt-4 mt-md-0">
        <input type="submit" class="btn btn-secondary" name="submit" onclick='return(confirm("Procéder à la création ?"));' value="Consulter"/>
      </div>
    </div>
  </form>
  </p>
</div>

<div class="my-5">
  <h2 class="title">Préparation d'un TIV</h2>
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

  <div>
    <p class=""><i class="fa fa-file-text-o" aria-hidden="true"></i> A noter que la préparation d'un TIV consiste à pré-affecter les blocs aux différentes personnes qui feront plus tard le TIV.</p>
  </div>
</div>
<div class="my-5">
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
</div>
