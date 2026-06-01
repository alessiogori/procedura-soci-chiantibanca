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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<style type="text/css">
  @import "css/fontawesome-free/css/all.min.css";
</style> 

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	// "order": [[ 3, "asc" ]],
          	"lengthMenu": [ 10, 25, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   	

<?php

$adesso = date('d/m');

// Controllo se la richiesta arriva   
if (!isset($_GET['filiale']))
    {$condizionefiliale = '';
     $titolofiliale = '';
     $filiale = '';
     $area = '';
     $rif = '';
    }
    else
    {
  // da un FILIALE
     if (!isset($_GET['area']) OR ($_GET['area']) == "")   
     {    
     $condizionefiliale = 'AND s.codfil in ('.$_GET['filiale'].')';
     $condizionefiliale2 = 'AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Filiale '.$_GET['filiale'];  
     $filiale = $_GET['filiale'];
     $rif = 'Filiale';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = 'AND s.codfil in ('.$_GET['filiale'].')';
     $condizionefiliale2 = 'AND Filiale in '.$_GET['filiale'].'';
     $titolofiliale = ' Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $rif = 'Area';
     }
    }


if ($_GET['action'] == 'list') 

{

$select = "	SELECT s.prot, s.codfil, s.cag, s.int1socio, s.nAzTot, dataNasc, dataEntrata, dataPrimoRapporto, 
			CC_num, RACDIR_tot, RACGEST_tot, sum(RACDIR_tot + RACGEST_tot) as RAC_Totale, dipendente
			FROM tab_soci_as37 as s LEFT JOIN tab_volumi as v
			ON s.cag = v.cag
			LEFT JOIN tab_xls_ammissioni AS a ON s.cag = a.cag
			WHERE s.nAzTot = ".$_GET['azioni']."
			".$condizionefiliale."
			AND s.statoVAL not in ('E','S','N')
			AND (a.Pac = 'N' OR a.Pac is null)
			GROUP by s.cag
			ORDER BY s.codfil, RAC_Totale desc
			";
//echo $select;
logquery ($select);  
$querydati = mysqli_query($connection, $select);

$quote_mancanti  = 33 - $_GET['azioni'];
$valore_mancante = $quote_mancanti * 30.33;

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Soci con '.$_GET['azioni'].' Azioni </h4>
      <p class="m-2 font-weight-bold text-success">Quote mancanti al minimo: nr.<b>'.$quote_mancanti.'</b> per <b>&euro; '.number_format($valore_mancante,2,',','.').'</b></p>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:center; font-size:13px;">Filiale</th>
				<th style="text-align:center; font-size:13px;">CAG</th>
				<th style="text-align:center; font-size:13px;">Nominativo</th>
				<th style="text-align:center; font-size:13px;">Azioni</th>
				<th style="text-align:center; font-size:13px;">Data Nascita</th>
				<th style="text-align:center; font-size:13px;">Data Entrata</th>
				<th style="text-align:center; font-size:13px;">Data Primo Rap</th>
				<th style="text-align:center; font-size:13px;">Qtà C/C</th>
				<th style="text-align:right; font-size:13px;">Raccolta Diretta</th>
				<th style="text-align:right; font-size:13px;">Raccolta Indiretta</th>
				<th style="text-align:right; font-size:13px;">Totale Raccolta</th>
			</tr>
		</thead>
		<tbody>
';	

while($dati=mysqli_fetch_array($querydati)){ 

if ($dati['dipendente'] == "S")
	{
		$raccolta_diretta = '<i class="fas fa-ghost fa-1x text-gray-300 col-auto" title="Dipendente"></i>';
		$raccolta_indiretta = '<i class="fas fa-ghost fa-1x text-gray-300 col-auto" title="Dipendente"></i>';
		$raccolta_totale = '<i class="fas fa-ghost fa-1x text-gray-300 col-auto" title="Dipendente"></i>';
	}
else
	{
		$raccolta_diretta = number_format($dati['RACDIR_tot'],0,',','.');
		$raccolta_indiretta = number_format($dati['RACGEST_tot'],0,',','.');
		$raccolta_totale = number_format($dati['RAC_Totale'],0,',','.');
	}

echo "	<tr class='table-secondary'>
			<td align='center'>".$dati['codfil']."</td>
			<td align='center'>".$dati['cag']."</td>
			<td align='left'><a class='text-success' href='sqldati_schedasocio.php?id=".$dati['prot']."'>".$dati['int1socio']."</a></td>
			<td align='center'>".$dati['nAzTot']."</td>
			<td align='center'>".$dati['dataNasc']."</td>
			<td align='center'>".$dati['dataEntrata']."</td>
			<td align='center'>".$dati['dataPrimoRapporto']."</td>
			<td align='center'>".$dati['CC_num']."</td>
			<td align='right'>".$raccolta_diretta."</td>
			<td align='right'>".$raccolta_indiretta."</td>
			<td align='right'>".$raccolta_totale."</td>
		</tr>";

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

$select = "	SELECT *
			FROM view_azioni_meno_minimo
			WHERE Area <> ''
			".$condizionefiliale2."
			ORDER BY area, filiale 
			";
//echo $select;
logquery ($select);  
$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Soci con quantità Azioni inferiori al minimo (33)</h4>
      <p class="m-2 font-weight-bold text-success">Nei conteggi sono COMPRESI i Soci con <u>rateizzazione</u> in corso; nel dettaglio tali Soci non vengono riportati</p>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:center; font-size:10px;">Filiale</th>
				<th style="text-align:left; font-size:10px;">Area</th>
				<th style="text-align:center; font-size:13px;">Qtà Soci</th>
				<th style="text-align:center; font-size:13px;">01 Az</th>
				<th style="text-align:center; font-size:13px;">02 Az</th>
				<th style="text-align:center; font-size:13px;">03 Az</th>
				<th style="text-align:center; font-size:13px;">04 Az</th>
				<th style="text-align:center; font-size:13px;">05 Az</th>
				<th style="text-align:center; font-size:13px;">06 Az</th>
				<th style="text-align:center; font-size:13px;">07 Az</th>
				<th style="text-align:center; font-size:13px;">08 Az</th>
				<th style="text-align:center; font-size:13px;">09 Az</th>
				<th style="text-align:center; font-size:13px;">10 Az</th>
				<th style="text-align:center; font-size:13px;">11 Az</th>
				<th style="text-align:center; font-size:13px;">12 Az</th>
				<th style="text-align:center; font-size:13px;">13 Az</th>
				<th style="text-align:center; font-size:13px;">14 Az</th>
				<th style="text-align:center; font-size:13px;">15 Az</th>
				<th style="text-align:center; font-size:13px;">16 Az</th>
				<th style="text-align:center; font-size:13px;">17 Az</th>
				<th style="text-align:center; font-size:13px;">18 Az</th>
				<th style="text-align:center; font-size:13px;">19 Az</th>
				<th style="text-align:center; font-size:13px;">20 Az</th>
				<th style="text-align:center; font-size:13px;">21 Az</th>
				<th style="text-align:center; font-size:13px;">22 Az</th>
				<th style="text-align:center; font-size:13px;">23 Az</th>
				<th style="text-align:center; font-size:13px;">24 Az</th>
				<th style="text-align:center; font-size:13px;">25 Az</th>
				<th style="text-align:center; font-size:13px;">26 Az</th>
				<th style="text-align:center; font-size:13px;">27 Az</th>
				<th style="text-align:center; font-size:13px;">28 Az</th>
				<th style="text-align:center; font-size:13px;">29 Az</th>
				<th style="text-align:center; font-size:13px;">30 Az</th>
				<th style="text-align:center; font-size:13px;">31 Az</th>
				<th style="text-align:center; font-size:13px;">32 Az</th>
			</tr>
		</thead>
		<tbody>
';	

while($dati=mysqli_fetch_array($querydati)){ 

echo "	<tr class='table-secondary'>
			<td align='center'>".$dati['Filiale']."</td>
			<td align='left'>".$dati['Area']."</td>
			<td align='center'>".$dati['QtaSoci']."</td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=1&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_01_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=2&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_02_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=3&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_03_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=4&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_04_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=5&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_05_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=6&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_06_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=7&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_07_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=8&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_08_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=9&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_09_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=10&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_10_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=11&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_11_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=12&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_12_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=13&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_13_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=14&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_14_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=15&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_15_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=16&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_16_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=17&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_17_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=18&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_18_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=19&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_19_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=20&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_20_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=21&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_21_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=22&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_22_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=23&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_23_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=24&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_24_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=25&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_25_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=26&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_26_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=27&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_27_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=28&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_28_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=29&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_29_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=30&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_30_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=31&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_31_Azioni']."</a></td>
			<td align='center'><a class='text-success' href='campagna_azioni.php?action=list&azioni=32&filiale=".$dati['Filiale']."&area=".$dati['Area']."'>".$dati['qta_con_32_Azioni']."</a></td>
		</tr>";

}

echo '		</tbody>
	</table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

// chiudo ELSE
}

	echo '<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';


?>