<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(0);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

    function clean_text($string)
    {
     $string = trim($string);
     $string = stripslashes($string);
     $string = htmlspecialchars($string);
     return $string;
    }


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	"order": [[ 3, "asc" ]],
          	"lengthMenu": [ 20, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   	

<?php
// DEFINIZIONE ANNO DI PARTENZA
$annostart = '2024';
$adesso = date('d/m');

if ($_GET['tipo'] == 'area') 
	{$fil = ' AND filiale in ('.$_GET['filiale'].')';}
elseif ($_GET['tipo'] == 'filiale') 
	{$fil = ' AND filiale = '.$_GET['filiale'];}
else
	{$fil = ' AND filiale <> 999';}

// ---------------------------
// Da qui faccio la statistica
// ---------------------------
if ($_GET['dettaglio'] != 'SI')
	{

$selectT_IN =  "SELECT
				tipologia, motivazione, count(*) as qta
				FROM tab_motivazioni 
				WHERE tipologia = 'IN'
				".$fil."
				GROUP BY tipologia, motivazione
				ORDER BY qta desc
				";
// echo $selectT_IN;
logquery ($selectT_IN);  
$querydatiT_IN = mysqli_query($connection, $selectT_IN);


$selectT_OUT =  "SELECT
				tipologia, motivazione, count(*) as qta
				FROM tab_motivazioni 
				WHERE tipologia = 'OUT'
				".$fil."
				GROUP BY tipologia, motivazione
				ORDER BY qta desc
				";
// echo $selectT_OUT;
logquery ($selectT_OUT);  
$querydatiT_OUT = mysqli_query($connection, $selectT_OUT);


echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-warning">Motivazioni IN/OUT Soci &nbsp;&nbsp;&nbsp; </h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">

    <!-- TABELLA GENERALE -->
    <table class="table table-bordered" width="100%" cellspacing="0">
    <tr>
    <td>

    <!-- CELLA TABELLA STATS IN -->
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
		<thead>
			<tr class="table-warning">
				<th colspan="3" style="text-align:left; font-size:16px;"><i class="fas fa-angle-double-right fa-1x col-auto"></i>Nuovi Ingressi</th>
			</tr>
			<tr class="table-primary">
				<th style="text-align:center; font-size:13px;">Motivazione</th>
				<th style="text-align:center; font-size:13px;">Qtà</th>
			</tr>
		</thead>
		<tbody>
';	

while($datiT_IN=mysqli_fetch_array($querydatiT_IN)){ 

echo "	<tr class='table-secondary'>
			<td align='left'>".$datiT_IN['motivazione']."</td>
			<td align='center'><a style='color:white;' href='motivazioni_check.php?dettaglio=SI&tipologia=IN&tipo=".$_GET['tipo']."&filiale=".$_GET['filiale']."&motivazione=".$datiT_IN['motivazione']."'>".$datiT_IN['qta']."</a></td>
		</tr>";

}

echo '		</tbody>
	</table>';

echo '
	</td>
    <td>

    <!-- CELLA TABELLA STATS OUT -->
       <table class="table table-bordered table-hover" width="100%" cellspacing="0">
		<thead>
			<tr class="table-warning">
				<th colspan="3" style="text-align:left; font-size:16px;"><i class="fas fa-angle-double-left fa-1x col-auto"></i>Uscite</th>
			</tr>
			<tr class="table-primary">
				<th style="text-align:center; font-size:13px;">Motivazione</th>
				<th style="text-align:center; font-size:13px;">Qtà</th>
			</tr>
		</thead>
		<tbody>
';	

while($datiT_OUT=mysqli_fetch_array($querydatiT_OUT)){ 

echo "	<tr class='table-secondary'>
			<td align='left'>".$datiT_OUT['motivazione']."</td>
			<td align='center'><a style='color:white;' href='motivazioni_check.php?dettaglio=SI&tipologia=OUT&tipo=".$_GET['tipo']."&filiale=".$_GET['filiale']."&motivazione=".$datiT_OUT['motivazione']."'>".$datiT_OUT['qta']."</a></td>
		</tr>";

}

echo '		</tbody>
	</table>';

echo '<!-- CHIUDO TABELLA GENERALE-->
	</td>
	</tr>
	</table>';

echo '	
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';


	echo '<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';

}

else

{

	echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-warning">Motivazioni IN/OUT Soci &nbsp;&nbsp;&nbsp; </h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        <table class="table table-bordered table-hover" width="80%" cellspacing="0">
		<thead>
			<tr class="table-warning">
				<th colspan="6" style="text-align:left; font-size:16px;">Dettaglio nominativi per Tipologia = '.$_GET['tipologia'].' e Motivazione = '.strtoupper($_GET['motivazione']).'</th>
			</tr>
			<tr class="table-primary">
				<th style="text-align:center; font-size:13px;">Filiale</th>
				<th style="text-align:center; font-size:13px;">CAG</th>
				<th style="text-align:left; font-size:13px;">Nominativo</th>
				<th style="text-align:center; font-size:13px;">Data Domanda</th>
				<th style="text-align:center; font-size:13px;">Note</th>
				<th style="text-align:center; font-size:13px;">Segnalazione</th>
			</tr>
		</thead>
		<tbody>
';	

if ($_GET['tipo'] == 'area') 
	{$fil = ' AND filiale in ('.$_GET['filiale'].')';}
elseif ($_GET['tipo'] == 'filiale') 
	{$fil = ' AND filiale = '.$_GET['filiale'];}
else
	{$fil = ' AND filiale <> 999';}

$select = "	
					SELECT id, nag, nominativo, tipologia, motivazione, note, filiale, operatore, 
								 data_segnalazione, attivo, data_domanda
				  FROM   tab_motivazioni 
				  WHERE  attivo = 'S'
				  AND tipologia = '".$_GET['tipologia']."'
					AND motivazione = '".$_GET['motivazione']."'
					".$fil."
					ORDER BY Nominativo
					";
// echo $select;
logquery ($select);  
$querydati = mysqli_query($connection, $select);

while($dati=mysqli_fetch_array($querydati)){ 

echo "	<tr class='table-secondary'>
			<td align='center'>".$dati['filiale']."</td>
			<td align='center'>".$dati['nag']."</td>
			<td align='left'>".$dati['nominativo']."</td>
			<td align='center'>".$dati['data_domanda']."</td>
			<td align='left'>".$dati['note']."</td>
			<td align='center'><small style='color:lightgray;'>".$dati['operatore']." (".$dati['data_segnalazione'].")</small></td>
		</tr>";

}

echo '		</tbody>
	</table>';

	echo '<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';
}

?>