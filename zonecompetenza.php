<?php
// *****************************************************************************
// Portale ChiantiBanca
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
// include ("docs/zonecompetenza.html");

if  (empty($_GET['cod_comune'])) 
{

?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	"order": [[ 0, "asc" ]],
          	"lengthMenu": [ 10, 25, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   		

<div id="load" style="display:none;">Loading... Please wait</div>

<?php

// ********************************************************
// LISTA ZONE DI COMPETENZA
// ********************************************************
$select = "	SELECT 
			PRO_COM_COMUNE as cod_comune,
			COMUNE as comune,
			COD_PRO_COMUNE as cod_provincia, 
			PROVINCIA_COMUNE as provincia,
			CAB as cab, 
			FILIALE as presenza_filiale,
			COMPETENZA as competenza,
			PA_3 as piazza
			FROM tab_comuni 
			WHERE PRO_COM_COMUNE <> 0
			GROUP BY PRO_COM_COMUNE
			ORDER BY COMUNE ";

logquery ($select);
$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Comuni della Toscana (e zone di competenza ChiantiBanca)</h4>
      &nbsp;&nbsp;<a class="text-success" href="https://www.tuttitalia.it/toscana/" target="_blank" class="alert-link">Ricerca dei Comuni italiani</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:left; font-size:13px;">Comune</th>
				<th style="text-align:left; font-size:13px;">Provincia</th>
				<th style="text-align:left; font-size:13px;">CAB</th>
				<th style="text-align:left; font-size:13px;">Piazza</th>
				<th style="text-align:center; font-size:13px;">Presenza<br>Filiale</th>
				<th style="text-align:center; font-size:13px;">Competenza</th>
				<th style="text-align:center; font-size:13px;">Comuni<br>Confinanti</th>
			</tr>
		</thead>
		<tbody>
';	

while($datizona=mysqli_fetch_array($querydati)){ 

    if ( ($datizona['competenza'] == 'S' ) && ($datizona['presenza_filiale'] == 'SI') )
    {$colore = 'background-color:#0A9D0A';}
    elseif ( ($datizona['competenza'] == 'S' ) && ($datizona['presenza_filiale'] == 'NO') )
    {$colore = 'background-color:#C67E0A';}
     else
    {$colore = '';  } 

	echo "	  <tr class='table-secondary'>   
				<td style='text-align:left;".$colore."'>".$datizona['comune']."</td>
				<td style='text-align:left;".$colore."'>".$datizona['provincia']."</td>
				<td style='text-align:left;".$colore."'>".$datizona['cab']."</td>
				<td style='text-align:left;".$colore."'>".$datizona['piazza']."</td>
				<td style='text-align:center;".$colore."'>".$datizona['presenza_filiale']."</td>
				<td style='text-align:center;".$colore."'>".$datizona['competenza']."</td>
				<td style='text-align:center; font-size:11px; text-decoration: none;".$colore."'>
				<a href='zonecompetenza.php?cod_comune=".$datizona['cod_comune']."&comune=".$datizona['comune']."'>
				<i class='fas fa-arrows-alt fa-2x text-gray-300 col-auto' style='color:blue;' title='Comuni confinanti'></i>
				</a></td>
				
				";
				
	echo '  </tr>'; 
 } 

echo '		</tbody>
	</table>
	</div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

}
else
{
    
?>
    
    <!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	"order": [[ 0, "asc" ]],
          	"lengthMenu": [ 10, 25, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   		

<div id="load" style="display:none;">Loading... Please wait</div>

<?php
$cod_comune = $_GET['cod_comune'];
$comune = $_GET['comune'];

// ********************************************************
// LISTA ZONE DI COMPETENZA ADIACENTI
// ********************************************************
$select = "	SELECT 
			PRO_COM_COMUNE as cod_comune,
			COMUNE as comune,
			COD_PRO_COMUNE as cod_provincia, 
			PROVINCIA_COMUNE as provincia,
            PRO_COM_COMUNE_ADIACENTE as cod_comune_adiacente, 
            COMUNE_ADIACENTE as comune_adiacente, 
			CAB as cab, 
			FILIALE as presenza_filiale ,
			COMPETENZA as competenza,
			PA_3 as piazza
			FROM tab_comuni 
			WHERE PRO_COM_COMUNE = ".$_GET['cod_comune']."
			ORDER BY COMUNE ";

logquery ($select);
$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Comuni adiacenti al comune di '.$comune.'</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:left; font-size:13px;">Comune Adiacente</th>
				<th style="text-align:center; font-size:13px;">Piazza</th>
				<th style="text-align:center; font-size:13px;">Presenza<br>Filiale</th>				
				<th style="text-align:left; font-size:13px;">Competenza</th>
			</tr>
		</thead>
		<tbody>
';	

while($datizona=mysqli_fetch_array($querydati)){ 

	$select_competenza = "	SELECT PRO_COM_COMUNE as cod_comune, 
							FILIALE as presenza_filiale ,
							COMPETENZA as competenza,
							PA_3 as piazza
							FROM tab_comuni 
							WHERE PRO_COM_COMUNE = ".$datizona['cod_comune_adiacente']."
							GROUP BY PRO_COM_COMUNE, FILIALE, COMPETENZA
							ORDER BY COMUNE ";
	$querydati_competenza = mysqli_query($connection, $select_competenza);
	while($datizona_competenza = mysqli_fetch_array($querydati_competenza)){ 

    if ( ($datizona_competenza['competenza'] == 'S' ) && ($datizona_competenza['presenza_filiale'] == 'SI') )
    {$colore1 = 'background-color:#0A9D0A';}
    elseif ( ($datizona_competenza['competenza'] == 'S' ) && ($datizona_competenza['presenza_filiale'] == 'NO') )
    {$colore1 = 'background-color:#C67E0A';}
     else
    {$colore1 = '';  } 

	echo "	  <tr class='table-secondary'>   
				<td style='text-align:left;".$colore1."'>".$datizona['comune_adiacente']."</a></td>
				<td style='text-align:left;".$colore1."'>".$datizona_competenza['piazza']."</a></td>
				<td style='text-align:left;".$colore1."'>".$datizona_competenza['presenza_filiale']."</a></td>
				<td style='text-align:left;".$colore1."'>".$datizona_competenza['competenza']."</a></td>
			  </tr>";

	}
 } 

 
echo '		</tbody>
	</table>
	</div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

}

?>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
