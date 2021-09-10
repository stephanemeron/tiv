<h1 class="main title mb-5"><i class='fa fa-sliders fa-2x'></i> Administration</h1>
<div class="row justify-content-center my-5">
	<div class="col-12 col-md-6 jumbotron">
      <h2 class="title">Déclaration d'un nouvel élément dans la base</h2>

      <form name="ajout_form" id="ajout_form" action="ajout_element.php" method="POST">
        <div class="row align-items-center">
          <div class="col-12 col-md-5">
            <p class="mb-0"><i class="fa fa-cogs" aria-hidden="true"></i> Type d'élément à déclarer :</p>
          </div>
          <div class="col-12 col-md-7">
            <select class="custom-select" id="element" name="element">
              <option value='bloc'>Bloc</option>
              <option value='robinet'>Robinet</option>
              <option value='stab'>Gillet Stabilisateur</option>
              <option value='detendeur'>Détendeur</option>
              <option value='inspecteur_tiv'>Inspecteur TIV</option>
            </select>
          </div>
          <div class="col-12 pt-3 text-right">
            <input type="submit" class="btn btn-lg btn-outline-primary" name="submit" onclick='return(confirm("Procéder à la création ?"));' value="Procéder à la création du nouvel élément"/>
          </div>
        </div>
      </form>
    </div>
</div>

<div class="row justify-content-center my-5">
	<div class="col-12 col-md-6 jumbotron">
      <h2 class="title">Consultation du journal</h2>
      <div class="row align-items-center">
        <div class="col-auto">
          <p class="mb-0"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Consulter les événements du journal.</p>
        </div>
        <div class="col-12 pt-3 text-right">
          <a class="btn btn-lg btn-outline-primary ml-md-3" href='affichage_element.php?element=journal_tiv'>Accès au journal</a>
        </div>
      </div>
    </div>
</div>

<div class="row justify-content-center my-5">
  	<div class="col-12 col-md-6 jumbotron">
      <h2 class="title">Consultation d'un TIV</h2>
      <form name="consultation_tiv" id="consultation_tiv" action="consultation_tiv.php" method="POST">
        <div class="row align-items-center">
          <div class="col-12 col-md-5">
            <p class="mb-0"><i class="fa fa-book" aria-hidden="true"></i> Choisissez votre TIV :</p>
          </div>
          <div class="col-12 col-md-7">
            <select class="custom-select" id="date-tiv-consultation" name="date_tiv" onselect="submit()">
              <?php
                include_once("connect_db.inc.php");
                $db_result = $db_con->query("SELECT date, count(id_bloc) FROM inspection_tiv GROUP BY date");
                while($result = $db_result->fetch_array()) {
                  print "  <option value='".$result["date"]."'>".$result["date"]." (".$result[1]." bloc(s) à contrôler)</option>\n";
                }
              ?>
            </select>
          </div>
          <div class="col-12 pt-3 text-right">
            <input type="submit" class="btn btn-lg btn-outline-primary" name="submit" onclick='return(confirm("Procéder à la création ?"));' value="Consulter"/>
          </div>
        </div>
      </form>
    </div>
</div>

<div class="row justify-content-center my-5">
  	<div class="col-12 col-md-6 jumbotron">
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
          $("#preparation_tiv_no_tech").validate({
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

        <div class="row align-items-center">
            <div class="col-12 mb-5">
                <form name="preparation_tiv" id="preparation_tiv_no_tech" action="preparation_tiv.php" method="POST">
                    <script>
                    $(function() {
                      $( "#admin-date-tiv-no-tech-selector" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yyyy-mm-dd',
                        appendText: '(dd-mm-yyyy)',
                        language: 'fr',
                        altFormat: 'dd-mm-yyyy'
                      });
                    });
                    </script>
                    <div class="row align-items-center">
                        <div class="col-12">
                            <p class="mb-0"><i class="fa fa-file-text-o" aria-hidden="true"></i> Préparation d'un TIV sans affectation de technicien.</p>
                        </div>
                        <div class="col-12 col-md-5">
                            <p class="mb-0"><i class="fa fa-calendar" aria-hidden="true"></i> Date de préparation du TIV :</p>
                        </div>
                        <div class="col-12 col-md-7">
                            <input type="text" name="date_tiv" id="admin-date-tiv-no-tech-selector" value="" class="w-100"/>
                        </div>
                        <div class="col-12 pt-3 text-right">
                            <input type="hidden" name="no-tech" value="1" />
                            <input type="submit" name="lancer" value="Procéder à la génération des fiches" class="btn btn-lg btn-outline-primary ml-md-3" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12">
                <hr/>
                <form name="preparation_tiv" id="preparation_tiv" action="preparation_tiv.php" method="POST" class="mt-5">
                    <script>
                    $(function() {
                      $( "#admin-date-tiv-selector" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yyyy-mm-dd',
                        appendText: '(dd-mm-yyyy)',
                        language: 'fr',
                        altFormat: 'dd-mm-yyyy'
                      });
                    });
                    </script>
                    <div class="row align-items-center">
                        <div class="col-12">
                            <p class="mb-0"><i class="fa fa-file-text-o" aria-hidden="true"></i> A noter que la préparation d'un TIV consiste à pré-affecter les blocs aux différentes personnes qui feront plus tard le TIV.</p>
                        </div>
                        <div class="col-12 col-md-5">
                            <p class="mb-0"><i class="fa fa-calendar" aria-hidden="true"></i> Date de préparation du TIV :</p>
                        </div>
                        <div class="col-12 col-md-7">
                            <input type="text" name="date_tiv" id="admin-date-tiv-selector" value="" class="w-100"/>
                        </div>
                        <div class="col-12 pt-3 text-right">
                            <input type="submit" name="lancer" value="Procéder à la pré-affectation" class="btn btn-lg btn-outline-primary ml-md-3" />
                        </div>
                        <div class="col-12 pt-3 text-right">
                            <a href="#" id="choix-tiv-toggle" class='btn btn-lg btn-outline-primary'>Choisir les personnes faisant le TIV</a>
                        </div>
                        <div id="choix-tiv" class="col-12 pt-3 text-right">
                            <select id="tivs" name="tivs[]" multiple='multiple' class="custom-select">
                                <?php
                                    include_once("connect_db.inc.php");
                                    $db_result = $db_con->query("SELECT id,nom,actif FROM inspecteur_tiv ORDER BY nom");
                                    while($result = $db_result->fetch_array()) {
                                      print "  <option value='".$result["id"]."'".($result["actif"] === "oui" ? " selected" : "").">".$result["nom"]."</option>\n";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
