<?php
include_once('configuration.inc.php');
# Si parametre _GET present, on est peut-être utilisé par ajout_element.php
if(array_key_exists("element", $_GET)) {
  $element = $_GET['element'];
  $id = $_GET['id'];
}
if(!isset($show_additional_information)) $show_additional_information = 1;
$title = "Edition d'un $element - club $nom_club";
include_once('head.inc.php');
include_once('definition_element.inc.php');
include_once('connect_db.inc.php');

$edit_class = get_element_handler($element, $db_con);

print $edit_class->getNavigationUrl();

if($show_additional_information && $extra_info = $edit_class->getExtraInformation($id)) {
  $bg_alert = "info";
  if($extra_info["bg_color"]){
    $bg_alert = $extra_info["bg_color"];
  }
  print "<div class=\"alert alert-".$bg_alert."\" role=\"alert\">";
    print "<h1 class='title'>Informations supplémentaires</h1>";
    print $extra_info["extra_info"];
  print "</div>";
}
print "<h2 class='title'>".$edit_class->getEditLabel()."</h2>
<script type='text/javascript'>
  var retour;
  $.validator.messages.required = 'Champ obligatoire';
  $(document).ready(function(){
    $(':submit').click(function () {
      if(this.name == 'delete') {
        if(confirm(\"".$edit_class->_delete_message."\")) {
          $.post('delete.php', $('#edit_form').serialize(), function(data) {
            $('#results').html(data);
            setTimeout('window.location.href = \"affichage_element.php?element=$element\";', 1000);
          });
        }
        return false;
      } else {
        $('#edit_form').validate({
    ".$edit_class->getFormsRules().",
          submitHandler: function(form) {
            $.post('process_element.php', $('#edit_form').serialize(), function(data) {
              $('#results').html(data);
                //setTimeout('window.location.href = \"edit.php?".$edit_class->getURLReference($id)."\";', 1000);
            });
          }
        });
      }
    });
  });
</script>";
print "<fieldset><legend>".$edit_class->getLegend($id)."</legend>";
print "<p id=\"results\"></p>";
$form_source = $edit_class->constructEditForm($id, "edit_form");
if($form_source) {
  print $form_source;
} else {
  print "<script type='text/javascript'>
window.location.href='affichage_element.php?element=$element';
</script>";
}
print "</fieldset>";
print get_journal_entry($db_con, $id, $element);

if($extra_operation = $edit_class->getExtraOperation($id)) {
  print "<h2>Opérations supplémentaires</h2>";
  print $extra_operation;
}

print $edit_class->getNavigationUrl();
include_once('foot.inc.php');
?>
