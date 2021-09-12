<?php
require_once("definition_element.inc.php");
require_once("connect_db.inc.php");
require_once('fpdf/fpdf.php');

class PdfTIV extends FPDF {
  var $_date;
  var $_db_con;
  function PdfTIV($date, $db_con) {
    $this->_date = $date;
    $this->_db_con = $db_con;
    parent::__construct();
    self::AliasNbPages();
  }

  function AddPage($orientation='', $size='', $rotation=0, $result=false)
  {
  	// Start a new page
  	if($this->state==3)
  		$this->Error('The document is closed');
  	$family = $this->FontFamily;
  	$style = $this->FontStyle.($this->underline ? 'U' : '');
  	$fontsize = $this->FontSizePt;
  	$lw = $this->LineWidth;
  	$dc = $this->DrawColor;
  	$fc = $this->FillColor;
  	$tc = $this->TextColor;
  	$cf = $this->ColorFlag;
  	if($this->page>0)
  	{
  		// Page footer
  		$this->InFooter = true;
  		$this->Footer();
  		$this->InFooter = false;
  		// Close page
  		$this->_endpage();
  	}
  	// Start new page
  	$this->_beginpage($orientation,$size,$rotation);
    $this->tMargin = 10;
  	// Set line cap style to square
  	$this->_out('2 J');
  	// Set line width
  	$this->LineWidth = $lw;
  	$this->_out(sprintf('%.2F w',$lw*$this->k));
  	// Set font
  	if($family)
  		$this->SetFont($family,$style,$fontsize);
  	// Set colors
  	$this->DrawColor = $dc;
  	if($dc!='0 G')
  		$this->_out($dc);
  	$this->FillColor = $fc;
  	if($fc!='0 g')
  		$this->_out($fc);
  	$this->TextColor = $tc;
  	$this->ColorFlag = $cf;
  	// Page header
  	$this->InHeader = true;
  	$this->Header($result);
  	$this->InHeader = false;
  	// Restore line width
  	if($this->LineWidth!=$lw)
  	{
  		$this->LineWidth = $lw;
  		$this->_out(sprintf('%.2F w',$lw*$this->k));
  	}
  	// Restore font
  	if($family)
  		$this->SetFont($family,$style,$fontsize);
  	// Restore colors
  	if($this->DrawColor!=$dc)
  	{
  		$this->DrawColor = $dc;
  		$this->_out($dc);
  	}
  	if($this->FillColor!=$fc)
  	{
  		$this->FillColor = $fc;
  		$this->_out($fc);
  	}
  	$this->TextColor = $tc;
  	$this->ColorFlag = $cf;
  }


  function Header($result_id_bloc=false) {
    global $logo_club;
    global $nom_club;
    //$this->SetY(5);
    //$this->Image('logo_club.png',10,6,30);

    //$this->Image("images/portrait_femme01.jpg",10,5,30,"jpg");
    //$this->Image('http://chart.googleapis.com/chart?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World',60,30,90,0,'PNG');
    $this->SetFont('Arial','B',10);
    if(array_key_exists("id_bloc", $_GET) || $result_id_bloc){
        if(array_key_exists("id_bloc", $_GET)){
            $id_bloc = $_GET['id_bloc'];
        }
        else{
            $id_bloc = $result_id_bloc;
        }
      $bloc_condition = "inspection_tiv.id_bloc = $id_bloc";
      $db_query = "SELECT  inspection_tiv.id_bloc AS id_bloc, inspection_tiv.id AS i_t_id, bloc.id_club AS bloc_id_club ".
                  "FROM inspection_tiv, bloc ";
      if(array_key_exists("date", $_GET)){
          $db_query .= "WHERE inspection_tiv.date = '".$_GET['date']."' AND ".$bloc_condition;
      }
      else{
          $db_query .= "WHERE ".$bloc_condition;
      }
      $db_result = $this->_db_con->query($db_query);
      $result = $db_result->fetch_array();
      //$this->Cell(145);
      // Affichage numéro fiche tiv
      $this->SetFont('Helvetica', 'B', 10);
      $this->Cell(22,18,utf8_decode("Fiche TIV n° "), 0, 0);
      $this->SetFont('Helvetica', '', 20);
      $this->Cell(10,16,$result["i_t_id"], 0, 0);
      $this->Image("images/logo-nemo.png",48,10,18,"png");
      $this->Image("images/logo-tiv.png",90,12,null,10,"png");
      $this->SetFont('Helvetica', 'I',10);
      //$this->Cell(0, 8, utf8_decode('Fiche TIV du '.$this->_date." - club $nom_club"), 'B', 0, 'C');
      $this->Cell(132, 18, utf8_decode('Numéro du bloc : '),0, 0, 'R');
      $this->SetFont('Helvetica','' ,40);

      $this->Cell(0,12,$result["bloc_id_club"],0, 1, 'R');
    }
    //$this->Cell(0, 8, utf8_decode('Fiche TIV du '.$this->_date." - club $nom_club".$logo_club), 'B', 0, 'C');
    //$this->Ln(11);
  }

  function Footer() {
    global $nom_club;
    $this->SetY(-10);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,6,utf8_decode('Inspection TIV du '.$this->_date." - Club $nom_club - Page ".$this->PageNo().'/{nb}'),0,0,'C');
  }

  // function contentHeader( $id_bloc = false){
  //   $this->AddPage();
  //   //$this->Ln(8);
  //   $this->SetFont('Helvetica', 'B',12);
  //   $this->Cell(0,8,utf8_decode("FICHE D'ÉVALUATION ET DE SUIVI D'UNE BOUTEILLE DE PLONGÉE"),0, 1, 'C');
  // }


  function addInspecteurResume() {

    $db_query = "SELECT inspection_tiv.id_inspecteur_tiv, inspecteur_tiv.nom, inspecteur_tiv.numero_tiv, COUNT(inspection_tiv.id_inspecteur_tiv) ".
                "FROM inspection_tiv,inspecteur_tiv ".
                "WHERE date = '".$this->_date."' AND inspecteur_tiv.id = inspection_tiv.id_inspecteur_tiv ".
                "GROUP BY inspection_tiv.id_inspecteur_tiv ".
                "ORDER BY inspecteur_tiv.nom";
    $db_result = $this->_db_con->query($db_query);
    $noInspecteur = false;
    if($db_result->num_rows > 0){
        $noInspecteur = true;
        $this->AddPage();
        $this->SetFont('Helvetica','B',16);

        $this->Cell(0, 10, utf8_decode("Informations relatives aux inspecteurs TIV du ".$this->_date.""),0,1);
        $this->Ln(6);
        $header = array("id", "Nom inspecteur", "numéro TIV", "Nombre de bouteille à inspecter");
        $w = array(10, 55, 40, 0);
        $this->SetFillColor(127,127,127);
        for($i = 0; $i < count($header); $i++) {
          $this->Cell($w[$i], 10, utf8_decode($header[$i]), 1, 0, 'C', 1);
        }
        $this->Ln();
        $this->SetFont('Helvetica','',13);
        while($result = $db_result->fetch_array()) {
          for($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i],10,utf8_decode($result[$i]), 1, 0);
          $this->Ln();
        }
        $this->Ln(5);
        $this->Cell(0, 5,utf8_decode("Vous trouverez les fiches récapitulatives de chaque inspecteur TIV dans les pages suivantes."), 0, 1);
    }
  }

  function addInspectionResume() {
    if(!$noInspecteur){
        $this->AddPage();
    }
    $this->SetFont('Helvetica','B',16);
    $this->Cell(0, 10, utf8_decode("Informations relatives à l'inspection TIV du ".$this->_date.""),0,1);

    $this->SetFont('Helvetica','',12);

    $db_query = "SELECT inspection_tiv.id, bloc.date_derniere_epreuve FROM inspection_tiv,bloc ".
                "WHERE date = '".$this->_date."' AND bloc.id = inspection_tiv.id_bloc" ;
    $db_result = $this->_db_con->query($db_query);
    $total = 0;
    $count_tiv = 0;
    $reepreuve = 0;
    $max_time_tiv = strtotime("-48 months", strtotime($this->_date));
    while($result = $db_result->fetch_array()) {
      $total++;
      $time = strtotime($result[1]);
      if($time > $max_time_tiv) $count_tiv++;
      else $reepreuve++;
    }
    $this->Cell(0,10,utf8_decode("Il est prévu d'inspecter $total blocs au total dont $reepreuve réépreuve(s) et ".$count_tiv." inspection(s) TIV."), 0, 1);
    $this->Cell(0,10,utf8_decode("Vous trouverez l'ensemble des fiches TIV dans les pages suivantes."), 0, 1);
  }

  function addResume() {
    $this->addInspecteurResume();
    $this->Ln(10);
    $this->addInspectionResume();
  }

  function addInspecteurFile() {
    global $nom_club;
    global $adresse_club;
    global $numero_club;
    $db_query = "SELECT DISTINCT id_inspecteur_tiv, inspecteur_tiv.nom, numero_tiv, adresse_tiv, telephone_tiv, id_inspecteur_tiv ".
                "FROM inspection_tiv, inspecteur_tiv ".
                "WHERE inspection_tiv.date = '".$this->_date."' AND id_inspecteur_tiv = inspecteur_tiv.id ".
                "GROUP BY inspection_tiv.id_inspecteur_tiv ORDER BY inspecteur_tiv.nom DESC";
    $db_result = $this->_db_con->query($db_query);
    while($result = $db_result->fetch_array()) {
      $this->addPage('L');
      $this->SetFont('Helvetica', '', 12);
      $this->Cell(0, 10, utf8_decode("FÉDÉRATION FRANÇAISE D'ÉTUDES ET DE SPORTS SOUS-MARINS"), 0, 0,'C');
      $this->SetFont('Helvetica', '', 10);
      $this->SetX(10);
      $this->MultiCell(20,4, utf8_decode("À retourner à la C.T.R."));
      $this->Ln(5);
      $this->Cell(110);
      $this->SetFont('Helvetica', '', 12);
      $this->Cell(48, 5, utf8_decode("Fiche de contrôle visuel"), 1, 0, 'C');
      $this->Ln(12);
      // Cartouche entête gauche
      $y = $this->GetY();
      $this->Cell(48, 5, utf8_decode("Date de la visite :"));
      $this->Cell(48, 5, date('d/m/Y', strtotime($this->_date)), 'B', 1, 'R');
      $this->Ln(3);
      $this->Cell(48, 5, utf8_decode("Nom du visiteur :"));
      $this->Cell(48, 5, utf8_decode($result[1]), 'B', 1, 'R');
      $this->Ln(3);
      $this->Cell(48, 5, utf8_decode("Numéro du T.I.V. :"));
      $this->Cell(48, 5, utf8_decode($result[2]), 'B', 1, 'R');
      $this->Ln(3);
      $this->Cell(48, 5, utf8_decode("Adresse du T.I.V. :"));
      $this->MultiCell(48, 5, utf8_decode($result[3]), 'B', 'R');
      $this->Ln(3);
      $this->Cell(48, 5, utf8_decode("Tél. du T.I.V. :"));
      $this->Cell(48, 5, utf8_decode($result[4]), 'B', 1, 'R');
      // Cartouche entête droit
      $this->Line(120, 45, 120, 90);
      $this->SetXY(130, $y);
      $this->Cell(34, 5, utf8_decode("Nom du club :"));
      $this->Cell(28, 5, utf8_decode($nom_club), 'B', 0, 'C');
      $this->Cell(5);
      $this->Cell(28, 5, utf8_decode("Numéro :"));
      $this->Cell(28, 5, utf8_decode($numero_club), 'B', 1, 'C');
      $this->Ln(2); $this->SetX(130);
      $this->Cell(34, 5, utf8_decode("Adresse du club :"));
      $this->MultiCell(89, 5, utf8_decode($adresse_club), 'B', 'C');
      $this->Ln(2); $this->SetX(127);
      $y = $this->GetY();
      $this->Cell(128, 24, "", 1);
      $this->SetXY(130, $y + 3);
      $this->Cell(58, 5, utf8_decode("Nombre de bouteille acceptées :"));
      $db_query = "SELECT COUNT(inspection_tiv.id_bloc) ".
                  "FROM inspection_tiv ".
                  "WHERE id_inspecteur_tiv = ".$result[5]." AND decision = 'OK' AND date = '".$this->_date."'";
      $db_count = $this->_db_con->query($db_query);
      $count = $db_count->fetch_array();
      $this->Cell(10, 5, utf8_decode($count[0]), 'B', 1, 'R');
      $this->Ln(3); $this->SetX(130);
      $this->Cell(58, 5, utf8_decode("Signature du T.I.V. :"));
      // Affichage du tableau récapitulant les bouteilles inspectées par le TIV
      $this->Ln(17);
      $this->addInspecteurFileBlocsInformations($result[0]);
    }
  }

  function addInspecteurFileBlocsInformationsTableHeader() {
    $bloc_header = array("Fabricant" => 27, "Marque" => 37, "Numéro bouteille" => 27,
      "Date première épreuve" => 24, "Date dernière épreuve" => 24, "Date dernière visite" => 24,
      "Observations lors de la visite" => 53, "Décision" => 27, "Commentaires" => 27);
    $sub_header = array("Observations lors de la visite" => array("Extérieur" => 17, "Intérieur" => 19, "Filetage" => 17));
    $this->SetFont('Helvetica', '', 10);
    $this->SetFillColor(192,192,192);
    $y = $this->GetY();
    foreach(array_keys($bloc_header) as $row) {
      $size = 12;
      if($this->GetStringWidth($row) > 29) $size /= 2;
      $x = $this->GetX();
      $this->MultiCell($bloc_header[$row], $size, utf8_decode($row), 1, 'C', 1);
      if(array_key_exists($row, $sub_header)) {
        $this->SetY($y + $size);
        $this->SetX($x);
        foreach(array_keys($sub_header[$row]) as $sub_row) {
          $this->Cell($sub_header[$row][$sub_row], $size, utf8_decode($sub_row), 1, 0, 'C', 1);
        }
      }
      $this->SetY($y);
      $this->SetX($x + $bloc_header[$row]);
    }
    $this->Ln();
  }
  function addInspecteurFileBlocsInformations($id_inspecteur) {
    $this->addInspecteurFileBlocsInformationsTableHeader();
    $to_retrieve = array("constructeur" => 27, "marque" => 37, "numero" => 27, "date_premiere_epreuve" => 24,
                         "date_derniere_epreuve" => 24, "date" => 24, "etat_exterieur" => 17, "etat_interieur" => 19,
                         "etat_filetage" => 17, "decision" => 27, "remarque" => 27);
    $db_query = "SELECT ".implode(",", array_keys($to_retrieve))." ".
                "FROM inspection_tiv, bloc ".
                "WHERE inspection_tiv.date = '".$this->_date."' AND id_inspecteur_tiv = $id_inspecteur AND bloc.id = id_bloc";
    $db_result = $this->_db_con->query($db_query);
    // Compteur pour savoir le nombre de ligne que nous pouvons créer
    $max_line_count = 8; // 8 lignes pour la première page puis 14 sur une page vierge
    $page_line_count = 1;
    while($result = $db_result->fetch_array()) {
      foreach(array_keys($to_retrieve) as $elt) {
        $this->Cell($to_retrieve[$elt], 10, utf8_decode($result[$elt]), 1, 0, 'C');
      }
      $this->Ln();
      if($page_line_count++ >= $max_line_count) {
        $page_line_count = 0;
        $max_line_count = 14;
        $this->AddPage('L');
        $this->addInspecteurFileBlocsInformationsTableHeader();
      }
    }
  }

  function addBlocAlert($id_bloc) {
    $this->SetFont('Helvetica', 'B', 14);
    $this->SetTextColor(255, 0, 0);
    $this->SetDrawColor(255, 0, 0);
    $this->SetLineWidth(0.6);
    $db_query = "SELECT date_derniere_epreuve,date_dernier_tiv ".
                "FROM bloc ".
                "WHERE id = $id_bloc";
    $db_result = $this->_db_con->query($db_query);
    $result = $db_result->fetch_array();
    $date_epreuve = strtotime($result[0]);
    $date_prochaine_epreuve = strtotime("+5 years", $date_epreuve);
    $date_helveticatamp = strtotime($this->_date);
    if($date_epreuve < strtotime("-55 months", $date_helveticatamp)) {
      //$this->SetXY(130, 21);
      $this->Cell(0, 8, utf8_decode("Réépreuve avant le ".date("d/m/Y", $date_prochaine_epreuve)), 1, 0, 'C');
    }
    $this->SetTextColor(0, 0, 0);
    $this->SetDrawColor(0, 0, 0);
    $this->SetLineWidth(.567/$this->k);
    $this->Ln();
  }

  function addBlocFile($id_bloc = false) {
    $bloc_condition = "";
    if($id_bloc) $bloc_condition = "AND id_bloc = $id_bloc";
    $db_query = "SELECT inspection_tiv.id, id_bloc, id_inspecteur_tiv, decision ".
                "FROM inspection_tiv ".
                "WHERE inspection_tiv.date = '".$this->_date."'".
                $bloc_condition ;

    $db_result = $this->_db_con->query($db_query);
    while($result = $db_result->fetch_array()) {
      // Affichage de l'entête de la fiche (capacité du bloc, date des réépreuves etc.)
      //$this->AddPage();
      $this->AddPage('','',0,$result[1]);
      //$this->Ln(8);
      $this->SetFont('Helvetica', 'B',11);
      $this->Cell(0,8,utf8_decode("FICHE D'ÉVALUATION ET DE SUIVI D'UNE BOUTEILLE DE PLONGÉE"),0, 1, 'R');
      $this->addBlocInformation($result[1]);

      // Affichage d'un message d'alerte en cas de dépassement de la date d'épreuve/tiv sur le bloc
      $this->addBlocAlert($result[1]);

      $this->addBlocDonnees($result[1]);
      $this->AddPage();
      $this->Cell(0,8,"",0, 1, 0);
      /*foreach(array("exterieur", "interieur") as $element)
        $this->addAspectBlocInformation($result[0], $element);*/
      //$this->addBlocRobinet($result[1]);
      /*foreach(array("filetage") as $element)
        $this->addAspectBlocInformation($result[0], $element);*/
      $this->addRobinetterie($result[1]);
      /*foreach(array("robineterie") as $element)
        $this->addAspectBlocInformation($result[0], $element);*/
      //$this->addCommentaireRobinetterie($result[0]);
      // Ligne de séparation
      //$this->Cell(0,3,"", 'B', 1, 1);
      $this->Ln(6);
      $this->addCommentaire($result[0]);

      $this->Ln(6);
      if($result["id_inspecteur_tiv"] != NULL){
          $db_query_inspecteur = "SELECT id, numero_tiv, nom ".
                          "FROM inspecteur_tiv ".
                          "WHERE  ".$result["id_inspecteur_tiv"]." = inspecteur_tiv.id ".
                          "ORDER BY inspecteur_tiv.nom DESC";
          $db_result_inspecteur = $this->_db_con->query($db_query_inspecteur);
          while($result_inspecteur = $db_result_inspecteur->fetch_array()) {

              $this->addResultatInspection($result_inspecteur);
          }
      }
      else{
          $this->addResultatInspection();
      }
      // Information concernant l'inspection TIV
      /*$this->SetFont('Helvetica', 'B', 14);
      $this->Cell(45,10,utf8_decode("Vérificateur TIV n° "), 0, 0);
      $this->SetFont('Helvetica',  '', 12);
      $this->Cell($this->GetStringWidth($result[2]) + 2,8,$result[2], 1, 0);
      $this->Cell(3);*/
      // Affichage numéro fiche tiv
      //$this->SetFont('Helvetica', 'B', 14);
      //$this->Cell(30,10,utf8_decode("Fiche TIV n° "), 0, 0);
      //$this->SetFont('Helvetica',  '', 12);
      //$this->Cell($this->GetStringWidth($result[0]) + 2,8,$result[0], 1, 1);
      //foreach(array("exterieur", "interieur", "filetage", "robineterie") as $element)
      //  $this->addAspectInformation($result[0], $element);
      // Ligne de séparation
      //$this->Cell(0,2,"", 'B', 1, 1);
      // Conclusion + signature
      /*$this->Ln(1);
      $this->SetFont('Helvetica',  'B', 12);
      $this->Cell(30,8,"Conclusions : ", 0, 0);
      $this->Cell(17,10,utf8_decode($result[3]), 1, 0);
      $this->Cell(10);
      $this->Cell(30,8,"Signatures : ", 0, 0);
      $this->MultiCell(60,10,utf8_decode($result[4])." ", 1, 'C');*/
      // Affichage d'un message d'alerte en cas de dépassement de la date d'épreuve/tiv sur le bloc
      //$this->addBlocAlert($result[1]);
    }
  }

  function resetDate($date){
    $arrayDate = explode('-',$date);
    return $arrayDate[2]."/".$arrayDate[1]."/".$arrayDate[0];
  }

  function addOneYear($date){
    $newDate = date('Y-m-d', strtotime($date. ' + 1 year'));
    $arrayDate = explode('-',$newDate);
    return $arrayDate[2]."/".$arrayDate[1]."/".$arrayDate[0];
  }

  function addBlocInformation($id_bloc) {

    //global $bloc_filetage;
    //global $robinet_filetage_sortie;

    $db_query = "SELECT bloc.id, bloc.id_club, bloc.nom_proprietaire, bloc.numero, bloc.constructeur, bloc.marque, bloc.capacite, bloc.date_premiere_epreuve, bloc.date_derniere_epreuve, bloc.date_dernier_tiv, bloc.pression_service, bloc.pression_epreuve, bloc.id_robinet, bloc.filetage, robinet.id, robinet.marque AS r_marque, robinet.serial_number AS r_serial_number, robinet.filetage AS r_filetage, robinet.filetage_sortie AS r_filetage_sortie ".
                "FROM bloc, robinet ".
                "WHERE bloc.id ='$id_bloc' AND robinet.id=bloc.id_robinet";
    $db_result = $this->_db_con->query($db_query);
    $bloc = $db_result->fetch_array();

    // BLOC NOM PROPRIETAIRE
    $this->SetFont('Helvetica', 'IB', 10);
    //$this->Rect(10,37, 190, 15,'D');
    $this->Cell(30,10,utf8_decode("Nom propriétaire "), "LTB", 0,'L');
    $this->SetFont('Helvetica', 'B', 15);
    $this->Cell($this->GetPageWidth()-80,10, utf8_decode($bloc["nom_proprietaire"]), "TB", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(0,10,"", "TRB", 1, 'R');
    // FIN BLOC NOM PROPRIETAIRE

    // BLOC IDENTIFICATION BOUTEILLE + ROBINET
    //$this->Ln(8);
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,8,utf8_decode("IDENTIFICATION DE L'ÉQUIPEMENT"), 0, 1, 'C');

    $this->Cell(140,5,utf8_decode("BLOC"), 1, 0, 'C');
    $this->Cell(0,5,utf8_decode("ROBINET"), 1, 1, 'C');
    // largeur 190
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(25,5,utf8_decode("Constructeur"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(35,5,$bloc["constructeur"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(35,5,utf8_decode("Date 1ère épreuve"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(45,5,$this->resetDate($bloc["date_premiere_epreuve"]), 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(16,5,utf8_decode("Marque"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,5,$bloc["r_marque"], 1, 1, 'L');

    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(25,5,utf8_decode("Marque"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(35,5,$bloc["marque"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(35,5,utf8_decode("Date dernière qualif"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(45,5,$this->resetDate($bloc["date_derniere_epreuve"]), 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(16,5,utf8_decode("N° série"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,5,$bloc["r_serial_number"], 1, 1, 'L');

    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(25,5,utf8_decode("N° de série"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(35,5,$bloc["numero"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(35,5,utf8_decode("Capacité (L)"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(45,5,$bloc["capacite"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(16,5,utf8_decode("Fil. entrée"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,5,$bloc["r_filetage"], 1, 1, 'L');

    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(15,5,"PE (bar)", 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(15,5,$bloc["pression_epreuve"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(15,5,"PS (bar)", 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(15,5,$bloc["pression_service"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(35,5,"Filetage bloc", 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(45,5,$bloc["filetage"], 1, 0, 'L');
    $this->SetFont('Helvetica', 'I', 9);
    $this->Cell(16,5,utf8_decode("Fil. sortie"), 1, 0, 'L');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,5,$bloc["r_filetage_sortie"], 1, 1, 'L');

    //$this->addBlocAlert($id_bloc);

    $this->SetTextColor(255,0,0);
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(95,7,utf8_decode("PROCHAINE I.V. AVANT LE : "), 0, 0, 'R');

    $this->SetDrawColor(255,0,0);
    $this->SetLineWidth(0.6);
    $this->SetFont('Helvetica', 'B', 13);
    $this->Cell(0,7,$this->addOneYear($bloc["date_dernier_tiv"]), 1, 1, 'C');
    $this->SetTextColor(0,0,0);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(.567/$this->k);

    // FIN BLOC IDENTIFICATION BOUTEILLE + ROBINET
  }

  function addBlocDonnees($id_bloc) {

    //global $bloc_filetage;
    //global $robinet_filetage_sortie;

    $db_query = "SELECT bloc.id, bloc.id_club, bloc.nom_proprietaire, bloc.numero, bloc.constructeur, bloc.marque, bloc.capacite, bloc.date_premiere_epreuve, bloc.date_derniere_epreuve, bloc.date_dernier_tiv, bloc.pression_service, bloc.pression_epreuve, bloc.id_robinet, bloc.filetage, robinet.id, robinet.marque AS r_marque, robinet.serial_number AS r_serial_number, robinet.filetage AS r_filetage, robinet.filetage_sortie AS r_filetage_sortie ".
                "FROM bloc, robinet ".
                "WHERE bloc.id ='$id_bloc' AND robinet.id=bloc.id_robinet";
    $db_result = $this->_db_con->query($db_query);
    $bloc = $db_result->fetch_array();
    // BLOC DONNEES BOUTEILLES
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,5,"", 0, 1, 'C');
    $this->Cell(80,5,utf8_decode("CONSTAT"), 0, 0, 'C');
    $this->Cell(60,5,utf8_decode("DECISION"), 0, 0, 'C');
    $this->Cell(0,5,utf8_decode("REALISATION"), 0, 1, 'C');
    $this->Cell(0,2,"", "B", 1, 'C');
    $this->Cell(80,5,utf8_decode("BOUTEILLE"), "TL", 0, 'C');
    $this->Cell(60,5,"", "T", 0, 'C');
    $this->Cell(0,5,"", "RT", 1, 'C');

    $this->Cell(60,5,utf8_decode("Filetage : ").$bloc["filetage"], "L", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(10,5,utf8_decode("oui"), 1, 0, 'C');
    $this->Cell(10,5,utf8_decode("non"), 1, 0, 'C');
    $this->Cell(60,5,"", 0, 0, 'C');
    $this->Cell(20,5,"Date", 1, 0, 'C');
    $this->Cell(0,5,"Par", 1, 1, 'C');

    $this->SetFont('Helvetica', '', 10);
    $this->Cell(60,5,utf8_decode("Filetage col en bon état"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À vérifier avec tampon"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Filetage col légèrement oxydé"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Filets actifs détériorés"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(40,5,utf8_decode("BLOC REBUTÉ"), 1, 0, 'C');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("À faire"), 1, 0, 'C');
    //$this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"OK", 1, 0, 'C');
    $this->Cell(10,5,"NOK", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(60,5,"", "R", 0, 'L');
    $this->Cell(0,5,"", "R", 1, 'C');

    $this->Cell(60,5,utf8_decode("Tampon fileté entre pas (NEP)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(40,5,utf8_decode("BLOC REBUTÉ"), 1, 0, 'C');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Tampon lisse entre pas (NEP)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(40,5,utf8_decode("BLOC REBUTÉ"), 1, 0, 'C');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    //$this->Cell(80,5,"", 0, 0, 'C');
    //$this->Cell(60,5,"", 0, 0, 'C');
    $this->Cell(0,5,"", "RL", 1, 'C');

    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("Extérieur"), "L", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(10,5,utf8_decode("oui"), 1, 0, 'C');
    $this->Cell(10,5,utf8_decode("non"), 1, 0, 'C');
    $this->Cell(60,5,"", 0, 0, 'C');
    $this->Cell(20,5,"Date", 1, 0, 'C');
    $this->Cell(0,5,"Par", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Atteintes profondes"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(40,5,utf8_decode("REFUSÉ"), 1, 0, 'C');
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Peintures en bon état"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,"", 0, 0, 'C');
    $this->Cell(0,5,"", "RTB", 1, 'C');

    $this->SetFontSize(10);
    $this->Cell(60,5,utf8_decode("Cloques, écailles non corrodés"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("Retouche"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(10);
    $this->Cell(60,5,utf8_decode("Cloques, écailles corrodés"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer (*)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(10);
    $this->Cell(60,5,utf8_decode("Corrosion superficielle localisée"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer (*)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(10);
    $this->Cell(60,5,utf8_decode("Corrosion superficielle généralisée"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À sabler (*)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFont('Helvetica', 'B', 10);
    $this->Cell(80,5,utf8_decode("(*) Nettoyage: Élimination de la corrosion,"), "RTL", 0, 'C');
    $this->Cell(40,5,utf8_decode("Traitement Sablage"), "RTL", 0, 'C');
    $this->Cell(20,5,"", "RTL", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5, "", "RTL", 0, 'C');
    $this->Cell(0,5, "", "RTL", 1, 'C');

    $this->SetFont('Helvetica', 'B', 10);
    $this->Cell(80,5,utf8_decode("galvanisation et retouche peinture"), "RBL", 0, 'C');
    $this->Cell(40,5,utf8_decode("+ Peinture"), "RBL", 0, 'C');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", "RBL", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", "RBL", 0, 'C');
    $this->Cell(0,5,"", "RBL", 1, 'C');

    $this->Cell(0,5,"", "RL", 1, 'C');

    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("Intérieur"), "L", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(10,5,utf8_decode("oui"), 1, 0, 'C');
    $this->Cell(10,5,utf8_decode("non"), 1, 0, 'C');
    $this->Cell(60,5,"", 0, 0, 'C');
    $this->Cell(20,5,"", 0, 0, 'C');
    $this->Cell(0,5,"", "R", 1, 'C');

    $this->Cell(60,5,utf8_decode("Présence de résidus"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Sec"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À sécher"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Revêtement"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(40,5,"", 1, 0, 'L');
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Opaque"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À éliminer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->Cell(60,5,utf8_decode("Transparent adhérent"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À éliminer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');


    $this->SetFont('Helvetica', 'B', 11);
    //$this->Cell(0,5,"", 0, 1, 'C');

    //$this->Cell(80,5,utf8_decode("CONSTAT"), 0, 0, 'C');
    //$this->Cell(60,5,utf8_decode("DECISION"), 0, 0, 'C');
    //$this->Cell(0,5,utf8_decode("REALISATION"), 0, 1, 'C');
    //$this->Cell(0,5,"", "B", 1, 'C');
    //$this->Cell(80,5,utf8_decode("BOUTEILLE"), "RTL", 0, 'C');
    //$this->Cell(60,5,"", "RTL", 0, 'C');
    //$this->Cell(0,5,"", "RTL", 1, 'C');
    $this->Cell(80, 5, "", "L", 0, "C");
    $this->Cell(60, 5, "", 0, 0, "C");
    $this->Cell(35, 5, "", 0, 0, "C");
    $this->SetFont('Helvetica', '', 9);
    $this->Cell(0, 5,utf8_decode("Valeur"), "RTL",1, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("Paroi"), "LB", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(10,5,utf8_decode("oui"), 1, 0, 'C');
    $this->Cell(10,5,utf8_decode("non"), 1, 0, 'C');
    $this->Cell(60,5,"", "B", 0, 'C');
    $this->Cell(20,5,"Date", 1, 0, 'C');
    $this->Cell(15, 5,"Par", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 9);
    $this->Cell(0, 5,utf8_decode("(mm)"), "RBL",1, 'C');

    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("Oxydation"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(30,5,"Grenaillage", 1, 0, 'C');
    $this->Cell(30,5,"Mesure US", 1, 0, 'C');
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFont('Helvetica', '', 8);
    $this->Cell(60,5,utf8_decode("Superficielle uniforme (Critère 1)"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(30,5,"o", 1, 0, 'C');
    $this->SetFillColor(100,100,100);
    $this->Cell(30,5,"", 1, 0, 'C',1);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(15,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C',1);

    $this->SetFont('Helvetica', '', 8);
    $this->Cell(60,5,utf8_decode("Petites piqures réparties (Critère 2)"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(30,5,"o", 1, 0, 'C');
    $this->Cell(30,5,"", 1, 0, 'C');
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(15,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFont('Helvetica', '', 8);
    $this->Cell(60,5,utf8_decode("Piqures en ligne ou en zone (Critère 3)"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(30,5,"o", 1, 0, 'C');
    $this->Cell(30,5,"", 1, 0, 'C');
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(15,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFont('Helvetica', '', 8);
    $this->Cell(60,5,utf8_decode("Corrosion feuilletante localisée (Critère 4)"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(30,5,"REJET", 1, 0, 'C');
    $this->SetFillColor(100,100,100);
    $this->Cell(30,5,"", 0, 0, 'C', 1);
    $this->Cell(20,5,"", "RL", 0, 'C');
    $this->Cell(15,5,"", "RL", 0, 'C');
    $this->Cell(0,5,"", "RL", 1, 'C', 1);

    $this->SetFont('Helvetica', '', 8);
    $this->Cell(60,5,utf8_decode("Corrosion feuillante généralisée (Critère 5)"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(30,5,"REJET", 1, 0, 'C');
    $this->SetFillColor(100,100,100);
    $this->Cell(30,5,"", 0, 0, 'C', 1);
    $this->Cell(20,5,"", "RL", 0, 'C');
    $this->Cell(15,5,"", "RL", 0, 'C');
    $this->Cell(0,5,"", "RL", 1, 'C', 1);

    $this->SetFont('Helvetica', '', 8);
    $this->Cell(60,5,utf8_decode("Corrosion pulvérulente (Critère 6"), 1, 0, 'R');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(30,5,"REJET", 1, 0, 'C');
    $this->SetFillColor(100,100,100);
    $this->Cell(30,5,"", "B", 0, 'C', 1);
    $this->Cell(20,5,"", "RLB", 0, 'C');
    $this->Cell(15,5,"", "RLB", 0, 'C');
    $this->Cell(0,5,"", "RLB", 1, 'C', 1);

    // FIN BLOC DONNEES BOUTEILLES
  }

  function addBlocRobinet($id_bloc){

    global $bloc_filetage;
    global $robinet_filetage_sortie;

    $db_query = "SELECT bloc.id, bloc.id_club, bloc.nom_proprietaire, bloc.numero, bloc.constructeur, bloc.marque, bloc.capacite, bloc.date_premiere_epreuve, bloc.date_derniere_epreuve, bloc.date_dernier_tiv, bloc.pression_service, bloc.pression_epreuve, bloc.id_robinet, bloc.filetage, robinet.id, robinet.marque AS r_marque, robinet.serial_number AS r_serial_number, robinet.filetage AS r_filetage, robinet.filetage_sortie AS r_filetage_sortie ".
                "FROM bloc, robinet ".
                "WHERE bloc.id ='$id_bloc' AND robinet.id=bloc.id_robinet";
    $db_result = $this->_db_con->query($db_query);
    $bloc = $db_result->fetch_array();



    // FILETAGES BLOC ET ROBINET
    $this->Cell(0, 5, "", 0, 1);
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("Filetage"), "LT", 0, 'C');
    $this->Cell(0, 5, "", "TR", 1);

    $this->SetFont('Helvetica', 'B', 8);
    $this->Cell(20, 8, "BLOC", 1, 0);
    $count_bloc_filetage = count($bloc_filetage);
    $width_cell_bloc_filetage = floor(170 / ($count_bloc_filetage + 1));
    foreach($bloc_filetage as $option) {
      $this->SetFont('ZapfDingbats', '', 14);
      if($bloc["filetage"] == $option){
        $this->Cell(5,8,"8", "LTB", 0, 'C');
      }
      else{
        $this->Cell(5,8,"o", "LTB", 0, 'C');
      }
      $this->SetFont('Helvetica', '', 8);
      $this->Cell($width_cell_bloc_filetage - 5 ,8, $option,"TBR",0);
    }
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(5,8,"o", "LTB", 0, 'C');
    $this->SetFont('Helvetica', '', 8);

    $this->Cell(190 - (20 + ($count_bloc_filetage * $width_cell_bloc_filetage)) - 5 ,8, "Autre","TBR",1);

    $this->SetFont('Helvetica', 'B', 8);
    $this->Cell(40, 8, "Sortie ROBINET", 1, 0);
    $count_robinet_filetage_sortie = count($robinet_filetage_sortie);
    $width_cell_robinet_filetage_sortie = floor(150 / ($count_robinet_filetage_sortie + 1));
    foreach($robinet_filetage_sortie as $option) {
      $this->SetFont('ZapfDingbats', '', 14);
      if($bloc["r_filetage_sortie"] == $option){
        $this->Cell(5,8,"8", "LTB", 0, 'C');
      }
      else{
        $this->Cell(5,8,"o", "LTB", 0, 'C');
      }
      $this->SetFont('Helvetica', '', 8);
      $this->Cell($width_cell_robinet_filetage_sortie - 5 ,8, $option,"TBR",0);
    }
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(5,8,"o", "LTB", 0, 'C');
    $this->SetFont('Helvetica', '', 8);

    $this->Cell(190 - (40 + ($count_robinet_filetage_sortie * $width_cell_robinet_filetage_sortie)) - 5 ,8, "Autre","TBR",1);

    // FIN FILETAGES BLOC ET ROBINET
  }

  function addRobinetterie($id_bloc){

    $db_query = "SELECT bloc.id, bloc.id_club, bloc.nom_proprietaire, bloc.numero, bloc.constructeur, bloc.marque, bloc.capacite, bloc.date_premiere_epreuve, bloc.date_derniere_epreuve, bloc.date_dernier_tiv, bloc.pression_service, bloc.pression_epreuve, bloc.id_robinet, bloc.filetage, robinet.id, robinet.marque AS r_marque, robinet.serial_number AS r_serial_number, robinet.filetage AS r_filetage, robinet.filetage_sortie AS r_filetage_sortie ".
                "FROM bloc, robinet ".
                "WHERE bloc.id ='$id_bloc' AND robinet.id=bloc.id_robinet";
    $db_result = $this->_db_con->query($db_query);
    $bloc = $db_result->fetch_array();

    // ROBINETTERIE
    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(0,5,"", 0, 1, 'C');
    $this->Cell(80,5,utf8_decode("CONSTAT"), 0, 0, 'C');
    $this->Cell(60,5,utf8_decode("DECISION"), 0, 0, 'C');
    $this->Cell(0,5,utf8_decode("REALISATION"), 0, 1, 'C');
    $this->Cell(0,2,"", "B", 1, 'C');
    $this->Cell(80,5,utf8_decode("ROBINETTERIE"), "RTL", 0, 'C');
    $this->Cell(60,5,"", "RTL", 0, 'C');
    $this->Cell(0,5,"", "RTL", 1, 'C');

    $this->Cell(60,5,utf8_decode(""), "L", 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(10,5,utf8_decode("oui"), 1, 0, 'C');
    $this->Cell(10,5,utf8_decode("non"), 1, 0, 'C');
    $this->Cell(60,5,"", 0, 0, 'C');
    $this->Cell(20,5,"Date", 1, 0, 'C');
    $this->Cell(0,5,"Par", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Le robinet se démonte facilement"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Dépôt de rouille sur les filets"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Dépôt de rouille sur le fond"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À nettoyer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Filets entrées en bon état"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À changer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Filets de sortie du robinet en bon état"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À changer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Manoeuvre des volants facile"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(60,5,"", 1, 0, 'L');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", "LTB", 0, 'C');
    $this->Cell(0,5,"", "TRB", 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Démontage, nettoyage, graissage"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("Nettoyage US"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Changement Kit robinet"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 9);
    $this->Cell(40,5,utf8_decode("Quantité changée (1 ou 2)"), 1, 0, 'L');
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Volant bakélite en bon état"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À changer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFont('Helvetica', 'B', 11);
    $this->Cell(60,5,utf8_decode("À faire"), 1, 0, 'C');
    //$this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"OK", 1, 0, 'C');
    $this->Cell(10,5,"NOK", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(60,5,"", "R", 0, 'L');
    $this->Cell(0,5,"", "R", 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Bague lisse passe (entrée du robinet)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À changer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    $this->SetFontSize(9);
    $this->Cell(60,5,utf8_decode("Bague filetée passe (entrée du robinet)"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->Cell(10,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(40,5,utf8_decode("À changer"), 1, 0, 'L');
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(20,5,"o", 1, 0, 'C');
    $this->SetFont('Helvetica', '', 10);
    $this->Cell(20,5,"", 1, 0, 'C');
    $this->Cell(0,5,"", 1, 1, 'C');

    // FIN ROBINETTERIE
  }

  function addCommentaireRobinetterie($id_inspection) {

    $db_query = "SELECT id, remarque_robineterie ".
                "FROM inspection_tiv ".
                "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    //$status = inspection_tivElement::getPossibleStatus($element == "interieur");
    //$this->SetFont('Helvetica', 'BU', 12);
    //$this->Ln(8);
    //$this->Cell(33, 8, utf8_decode("État $label :"), 0, 0);
    $this->SetFont('Helvetica', 'B', 12);
    /*foreach($status as $state) {
      if(strlen($state) == 0) continue;
      $len = $this->GetStringWidth($state) + 2;
      $this->Cell($len, 8, utf8_decode($state), 0, 0);
      $this->Cell(5, 5, ($inspection["etat_$element"] == $state ? "X" : ""), 1, 0);
      $this->Cell(5);
    }*/
    //$this->Cell(5, 7, "", 0, 1);

    if($inspection["remarque_robineterie"]){
      $this->Cell(40,19,utf8_decode("Commentaires robinetterie :"), "LT", 0, "L");
      $this->Cell(0, 19, utf8_decode($inspection["remarque_robineterie"]), "TR", 1);
      $this->Cell(40,19,"", "LB", 0);
      $this->Cell(0, 19, "", "RB", 1);
    }
    else{
      $this->Cell(40,8,utf8_decode("Commentaires robinetterie :"), "LT", 0, "L");
      $this->Cell(0, 8, "", "TR", 1);
      $this->Cell(40,30,"", "LB", 0);
      $this->Cell(0, 30, "", "RB", 1);
    }

  }

  function addCommentaire($id_inspection) {

    $db_query = "SELECT id, remarque ".
                "FROM inspection_tiv ".
                "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    //$status = inspection_tivElement::getPossibleStatus($element == "interieur");
    //$this->SetFont('Helvetica', 'BU', 12);
    //$this->Ln(8);
    //$this->Cell(33, 8, utf8_decode("État $label :"), 0, 0);
    $this->SetFont('Helvetica', 'BU', 12);
    /*foreach($status as $state) {
      if(strlen($state) == 0) continue;
      $len = $this->GetStringWidth($state) + 2;
      $this->Cell($len, 8, utf8_decode($state), 0, 0);
      $this->Cell(5, 5, ($inspection["etat_$element"] == $state ? "X" : ""), 1, 0);
      $this->Cell(5);
    }*/
    //$this->Cell(5, 7, "", 0, 1);
    if($inspection["remarque"]){
      $this->Cell(40,12,utf8_decode("Commentaires général :"), "LT", 0, "L");
      $this->Cell(0, 12, utf8_decode($inspection["remarque"]), "TR", 1);
      $this->Cell(40,12,"", "LB", 0);
      $this->Cell(0, 12, "", "RB", 1);
    }
    else{
      $this->Cell(40,8,utf8_decode("Commentaires général :"), "LT", 0, "L");
      $this->Cell(0, 8, "", "TR", 1);
      $this->Cell(40,16,"", "LB", 0);
      $this->Cell(0, 16, "", "RB", 1);
    }

  }

  function addResultatInspection($result=false){
    $this->SetFont('Helvetica', 'B', 12);
    $this->Cell(60, 6, utf8_decode("Résultat de l'inspection"), "RTL",0, "L");
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(55, 6, utf8_decode("Nom du TIV"), "RTL",0,"L");
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(0, 6, utf8_decode("Signature du TIV"), "RTL",1,"L");

    $this->SetFont('Helvetica', "", 10);
    $this->Cell(20, 6, "", "L",0, "L");
    $this->Cell(30, 6, utf8_decode("Validée"), 0,0, "L");
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,6,"o", "R", 0, 'C');
    $this->SetFont('Helvetica', "", 10);
    $this->Cell(5, 6, "", 0, 0,"L");
    if($result){
        $this->Cell(55, 6, utf8_decode($result["nom"]), "RBL", 0,"L");
    }
    else{
        $this->Cell(55, 6, "", "RBL", 0,"L");
    }
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(0, 6, "", "RL",1,"L");

    $this->SetFontSize(10);
    $this->Cell(20, 6, "", "L",0, "L");
    $this->Cell(30, 6, utf8_decode("Non conforme"), 0,0, "L");
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,6,"o", "R", 0, 'C');
    $this->SetFont('Helvetica', "", 10);
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(55, 6, "", 0,0,"L");
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(0, 6, "", "RL",1,"L");

    $this->SetFontSize(10);
    $this->Cell(20, 6, "", "L",0, "L");
    $this->Cell(30, 6, utf8_decode("Refusée"), 0,0, "L");
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,6,"o", "R", 0, 'C');
    $this->SetFont('Helvetica', "B", 12);
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(55, 6, utf8_decode("Vérificateur TIV n° "), "RTL", 0,"L");
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(0, 6, "", "RL",1,"L");

    $this->SetFont('Helvetica', "", 10);
    $this->Cell(20, 6, "", "LB",0, "L");
    $this->Cell(30, 6, utf8_decode("Rebutée"), "B",0, "L");
    $this->SetFont('ZapfDingbats', '', 14);
    $this->Cell(10,6,"o", "RB", 0, 'C');
    $this->SetFont('Helvetica', "", 10);
    $this->Cell(5, 6, "", 0, 0,"L");
    if($result){
        $this->Cell(55, 6, utf8_decode($result["numero_tiv"]), "RBL",0,"L");
    }
    else{
        $this->Cell(55, 6, "", "RBL",0,"L");
    }
    $this->Cell(5, 6, "", 0, 0,"L");
    $this->Cell(0, 6, "", "RBL",1,"L");



  }

  function addAspectBlocInformation($id_inspection, $element) {
    $labels = array("interieur" => "intérieur", "exterieur" => "extérieur", "filetage"=>"filetage", "robineterie"=>"robinetterie");
    $label = $element;
    if(array_key_exists($element, $labels)) $label = $labels[$element];

    $db_query = "SELECT id, etat_$element, remarque_$element ".
                "FROM inspection_tiv ".
                "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    //$status = inspection_tivElement::getPossibleStatus($element == "interieur");

    $this->SetFont('Helvetica', 'BU', 12);
    //$this->Ln(8);
    //$this->Cell(33, 8, utf8_decode("État $label :"), 0, 0);
    //$this->SetFont('Helvetica', 'B', 12);
    /*foreach($status as $state) {
      if(strlen($state) == 0) continue;
      $len = $this->GetStringWidth($state) + 2;
      $this->Cell($len, 8, utf8_decode($state), 0, 0);
      $this->Cell(5, 5, ($inspection["etat_$element"] == $state ? "X" : ""), 1, 0);
      $this->Cell(5);
    }*/
    //$this->Cell(5, 7, "", 0, 1);
    $this->Cell(0,8,utf8_decode("Commentaire $label :"), "LTR", 1);
    $this->MultiCell(0, 14, ($inspection["remarque_$element"] ? utf8_decode($inspection["remarque_$element"]) : ""), "LBR", 1);
  }

  function addAspectInformation($id_inspection, $element) {
    $labels = array("interieur" => "intérieur", "exterieur" => "extérieur");
    $label = $element;
    if(array_key_exists($element, $labels)) $label = $labels[$element];

    $db_query = "SELECT id, etat_$element, remarque_$element ".
                "FROM inspection_tiv ".
                "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    $status = inspection_tivElement::getPossibleStatus($element == "interieur");

    $this->SetFont('Helvetica', 'BU', 12);
    $this->Ln(8);
    $this->Cell(33, 8, utf8_decode("État $label :"), 0, 0);
    $this->SetFont('Helvetica', 'B', 12);
    foreach($status as $state) {
      if(strlen($state) == 0) continue;
      $len = $this->GetStringWidth($state) + 2;
      $this->Cell($len, 8, utf8_decode($state), 0, 0);
      $this->Cell(5, 5, ($inspection["etat_$element"] == $state ? "X" : ""), 1, 0);
      $this->Cell(5);
    }
    $this->Cell(5, 7, "", 0, 1);
    $this->Cell(0,8,utf8_decode("Commentaire et action si état autre que bon :"), 0, 1);
    $this->MultiCell(0, 8, ($inspection["remarque_$element"] ? utf8_decode($inspection["remarque_$element"]) : " "), 1, 1);
  }
}

?>
