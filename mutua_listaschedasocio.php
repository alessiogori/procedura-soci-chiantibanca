<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
ini_set('max_execution_time', 0); 
// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Mi connetto al database MYSQL
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

$adesso = date('d/m');

if (empty($_POST['filiale'])) 
{$condizionefiliale = "WHERE 
			(( CONCAT(cognome,' ',nome) LIKE '%".$_POST['ricerca']."%' OR CODICEFISCALE LIKE '%".$_POST['ricerca']."%' ))";}
elseif ($_POST['filiale'] == 'full')
{$condizionefiliale = " WHERE filiale <> 990  ";}
else 
{$condizionefiliale = " WHERE filiale = ".$_POST['filiale']."  ";}

// ********************************************************
// DATI GENERALI
// ********************************************************

	// Preparo il CSV
	$contenutofile = '';
    $myfile = fopen("tmp/listasocimutua".$_POST['filiale'].".csv", "w");
    $contenutofile .= "ELENCO SOCI MUTUA\n";
    $contenutofile .= "Filiale;NAG;Nominativo;CodiceFiscale;ClasseTariffaria;SocioDal;Sesso;SocioBanca\n";


//				date_format(STR_TO_DATE(dataNascita, '%Y-%m-%d'),'%d/%m/%Y') AS DataNascita,
//				date_format(STR_TO_DATE(socioDal, '%Y-%m-%d'),'%d/%m/%Y') as sociodal,

$select = "	SELECT
							Filiale,
							tab_mutua.NAG, 
							CONCAT(cognome, ' ', nome) AS Nominativo,
							CodiceFiscale,
							ClasseTariffaria as Tariffa,
							tab_mutua.sesso,
							idsocio,
							sociodal,
							case 
							when socio_istituto = 1 then 'Socio Banca'
							when socio_istituto = 9 then 'Ex Socio Banca'
							else '' end as SocioBanca
						FROM
							tab_mutua left join sds_soci 
						ON tab_mutua.nag = sds_soci.nag
						".$condizionefiliale."
						GROUP BY tab_mutua.NAG,
							nominativo";

logquery ($select);
// echo $select;
$querydati = mysqli_query($connection, $select);

echo '
<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $(\'#dataTable\').DataTable( {
          	"order": [[ 2, "asc" ]],
          	"lengthMenu": [ 10, 25, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   		

<div id="load" style="display:none;">Loading... Please wait</div>

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3 ">
      <h4 class="m-2 font-weight-bold text-success">Soci ChiantiMutua</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-success">
				<th style="text-align:left; font-size:13px;">Filiale</th>
				<th style="text-align:left; font-size:13px;">NAG</th>
				<th style="text-align:left; font-size:13px;">Nominativo</th>
				<th style="text-align:center; font-size:13px;">Codice Fiscale</th>
				<th style="text-align:center; font-size:13px;">Classe Tariffaria</th>
				<th style="text-align:center; font-size:13px;">Socio dal</th>
				<th style="text-align:center; font-size:13px;">ChiantiBanca</th>
			</tr>
		</thead>
		<tbody>
';	

while($datisocio=mysqli_fetch_array($querydati)){ 

	if ($datisocio['SocioBanca'] == 'Socio Banca') 
	  {
       $esistenzasocioCB = 
	 			"<i class='fas fa-check fa-2x col-auto' style='color:#9FE2BF;'></i><span style='color:#444444;'>SI</span>";
	 		 $esistenzasocioCBvalore = 'SI';
    }
		else
		{
	     $esistenzasocioCB =
	     	"<i class='fas fa-times fa-2x col-auto' style='color:red;'></i><span style='color:#444444;'>NO</span>";
	 		 $esistenzasocioCBvalore = 'NO';

		}

	if ($datisocio['sesso'] == 'M') {$icosesso = '<img src="img/ico_man.png" height="20">&nbsp;';}
	else {$icosesso = '<img src="img/ico_woman.png" height="20">&nbsp;';}

	$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['idsocio']."'>".$datisocio['SocioBanca']."</a>";

	echo "	  <tr class='table-secondary'>   
				<td style='text-align:left;'>".$datisocio['Filiale']."</td>
				<td style='text-align:left;'>".$datisocio['NAG']."</td>
				<td style='text-align:left;'>".$icosesso." ".$datisocio['Nominativo']."</td>
				<td style='text-align:center;'>".$datisocio['CodiceFiscale']."</td>
				<td style='text-align:center;'>".$datisocio['Tariffa']."&nbsp;</td>
				<td style='text-align:center;'>".$datisocio['sociodal']."&nbsp;</td>
				<td style='text-align:center;'>".$linksocio."&nbsp;</td>
			";

	// echo '	<td style="text-align:center; font-size:11px; text-decoration: none;"><a href="modulistica_mutua.php?prot='.$datisocio['Prot'].'&cag='.$datisocio['CAG'].'&socio='.urlencode(stripslashes($datisocio['Nominativo'])).'&idsocio='.$datisocio['Prot'].'&tessera='.$datisocio['Tessera'].'&mutua=si'.'" title="Modelli precompilati '.$datisocio['Nominativo'].'"><i class="fas fa-file-signature fa-2x col-auto" style="color:#9FE2BF;"></i></a></td>';

	echo '  </tr>'; 

		// SCRIVO IL CSV
    $contenutofile .= $datisocio['Filiale'].";".$datisocio['NAG'].";".$datisocio['Nominativo'].";".$datisocio['CodiceFiscale'].";".$datisocio['Tariffa'].";".$datisocio['sociodal'].";".$datisocio['sesso'].";".$esistenzasocioCBvalore."\n";

}


	// CHIUDO CSV
    fwrite($myfile, $contenutofile);
    fclose($myfile);

echo '		</tbody>
	</table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';


?>

<center><a href="tmp/listasocimutua<?php echo $_POST['filiale'];?>.csv"><img src="img/google_docs.png"></a>&nbsp;<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
