<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title><?php print $title?></title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <style type="text/css" title="currentStyle">
          /*@import "css/page.css";
          @import "css/table.css";
          @import "css/site.css";
          @import "css/smoothness/jquery-ui.css";
          @import "css/magicsuggest-1.3.0-min.css";*/
          /*@import "css/bootstrap-grid.min.css";*/
          @import "css/bootstrap-reboot.min.css";
          @import "css/bootstrap.min.css";
          /*@import "css/datatable.min.css";*/
          @import "DataTables-1.10.22/css/dataTables.bootstrap4.min.css";
          /*@import "js/jQueryUI-1.12.1/themes/base/jquery-ui.css";*/ /* Pour mettre les text des boutons en phase avec bs4 */
          @import "css/magicsuggest.css";
          @import "font-awesome-4.7.0/css/font-awesome.css";
        </style>
          <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
        <style type="text/css" title="currentStyle">
          @import "select2-bootstrap4-theme-master/dist/select2-bootstrap4.css";
          @import "css/style.css";
        </style>
        <!--<script type="text/javascript" charset="utf-8" src="DataTables-1.9.4/media/js/jquery.js"></script>-->
        <!--<script type="text/javascript" charset="utf-8" src="DataTables-1.9.4/media/js/jquery.dataTables.js"></script>-->
        <!--<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.10.3.js"></script>-->
        <script type="text/javascript" charset="utf-8" src="js/jQuery-3.3.1/jquery-3.3.1.js"></script>
        <script type="text/javascript" charset="utf-8" src="DataTables-1.10.22/js/jquery.dataTables.js"></script>

        <script type="text/javascript" charset="utf-8" src="DataTables-1.10.22/js/dataTables.bootstrap4.min.js"></script>

        <script type="text/javascript" charset="utf-8" src="js/jQueryUI-1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" charset="utf-8" src="js/jquery.validate.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
        <!--<script type="text/javascript" charset="utf-8" src="js/magicsuggest-1.3.0-min.js"></script>-->
        <script type="text/javascript" charset="utf-8" src="js/magicsuggest.js"></script>
        <script type="text/javascript" charset="utf-8" src="js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="js/popper.js"></script>
        
        <script type="text/javascript" charset="utf-8" src="js/launch.js"></script>

    </head>
    <body>
        <div class="container-fluid">
            <?php
            foreach(array("connect_db.inc.php", "configuration.inc.php", "fpdf/fpdf.php", "logo_club.png") as $file) {
              if(!file_exists($file)) {
                print "<div class='error'>L'application n'est pas correctement installé (il manque le fichier $file).</div>";
                print "<div class='error'>Merci de suivre les instructions du fichier <a href='README'>README</a> avant de continuer.</div>";
                include_once("foot.inc.php");
                exit();
              }
            }
            ?>