<?php
class journal_tivElement extends TIVElement {
  function journal_tivElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#admin";
    $this->_parent_url_label = "<img src='images/admin.png' /> Administration";
    $this->_read_only = true;
    $this->_update_label = "";
    $this->_elements =  array("id" => "Réf.", "date" => "Date", "element" => "Élément", "id_element" => "Réf. élément", "message" => "Message");
    $this->_forms = array();
    $this->_forms_rules = '';
  }
  function updateRecord(&$record) {
    $record["element"] .= " <a href='edit.php?id=".$record["id_element"]."&element=".$record["element"]."'>[Accéder à la fiche ".$record["element"]."]</a>";
  }
}

function add_journal_entry(&$db_con, $id_element, $type_element, $message) {
  $id_element   = $db_con->escape_string($id_element);
  $type_element = $db_con->escape_string($type_element);
  $message      = $db_con->escape_string($message);
  $db_query = "INSERT INTO journal_tiv VALUES (0, SYSDATE(), '$type_element', $id_element, '$message')";
  return $db_con->query($db_query);
}

function get_journal_entry(&$db_con, $id, $element) {
  $db_query = "SELECT date,message FROM journal_tiv WHERE element = '$element' AND id_element = $id ORDER BY date DESC";
  $db_result = $db_con->query($db_query);
  $to_display = 0;
  $journal_entry = array();
  while($result = $db_result->fetch_array()) {
    $journal_entry []= "Le ".$result[0]." - ".$result[1];
  }
  if(count($journal_entry) == 0) $journal_message = "<p>Pas d'entrée dans le journal</p>";
  else $journal_message = "  <p>".implode("</p>\n  <p>", $journal_entry)."</p>\n";
  $journal = "<script>
  $(function() {
    $('#entrees-journal').hide();
    $('#entrees-journal-toggle').click(function() {
      $('#entrees-journal').toggle(400);
      return false;
    });
  });
</script>
<p><a href='#' id='entrees-journal-toggle'>Afficher le journal (".count($journal_entry)." entrée(s))</a></p>
<div id='entrees-journal'>
$journal_message
</div>\n";
  return $journal;
}
?>