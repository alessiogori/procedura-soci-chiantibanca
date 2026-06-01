<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

if (get_browser_name($_SERVER['HTTP_USER_AGENT']) == "Internet Explorer")
	{$imgext = "jpg";}
else
	{$imgext = "png";}

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="alert alert-dismissible alert-success">
        <h4 class="m-2 font-weight-bold text-dark">Elenco Comunicazioni</h4></td>
    </div>

<div class="container-fluid">
	
<?php

    $dir = "news/";
    foreach (scandir($dir) as $f) 
    
    {
      if ($f !== '.' and $f !== '..')
      {
              
            $path_parts = pathinfo($f); 
            if ($path_parts['extension'] == 'pdf') {$tipofile = '<i class="fas fa-file-pdf fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'doc') {$tipofile = '<i class="fas fa-file-word fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'docx') {$tipofile = '<i class="fas fa-file-word fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'xls') {$tipofile = '<i class="fas fa-file-excel fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'xlsx') {$tipofile = '<i class="fas fa-file-excel fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'ppt') {$tipofile = '<i class="fas fa-file-powerpoint fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'pptx') {$tipofile = '<i class="fas fa-file-powerpoint fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'txt') {$tipofile = '<i class="fas fa-file fa-1x text-gray-300 col-auto"></i>';}
            if ($path_parts['extension'] == 'csv') {$tipofile = '<i class="fas fa-file-csv fa-1x text-gray-300 col-auto"></i>';}

          $fname = $path_parts['filename'];
          echo $tipofile."<a href='news/".$f."' target='_blank' style='color:white;'>".$fname."</a> <br>";
      }
    }


?>

	</div>
	


</div>
<!-- /.container-fluid -->


<br/><br>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>



