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

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	"order": [[ 2, "asc" ]],
          	"lengthMenu": [ 20, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   	

<?php
// DEFINIZIONE ANNO DI PARTENZA
$annostart = '2024';
$adesso = date('d/m');


if (($_GET['start'] == "IN") && ($_GET['nag'] != ''))
	{ $tipologia = "('IN')";
	  $titolo = 'NUOVI INGRESSI'; 
	  $condizione = "	AND tipologia = 'IN' AND nag = ".$_GET['nag']." AND filiale = ".$_GET['filiale']." ";						
	}

elseif (($_GET['start'] == "IN") && ($_GET['nag'] == ''))
	{ $tipologia = "('IN')";
	  $titolo = 'NUOVI INGRESSI'; 
	  $condizione = "	AND tipologia = 'IN' AND filiale = ".$_GET['filiale']." ";						
	}

/*
elseif ($_GET['start'] == "OUT") 
	{ $tipologia = "('OUT')";
	  $titolo = 'USCITE'; 
	  $condizione = "	( str_to_date(s.DATA_USCITA,'%d/%m/%Y') >=  str_to_date('01/01/".$annostart."','%d/%m/%Y') )";
	  $filiale    = " AND Filiale_capofila = ".$_GET['filiale']." ";						
	  $esistenza  = " AND socio_istituto != 1 AND NOT EXISTS ";
	  $tipo_data  = "Data Uscita";
	}

if (($_GET['start'] == "banca_IN") && ($_GET['nag'] != ''))
	{ $tipologia = "('IN')";
	  $titolo = 'NUOVI INGRESSI'; 
	  $condizione = "	AND tipologia = 'IN' AND nag = ".$_GET['nag']." AND filiale <= 999 ";						
	}

elseif (($_GET['start'] == "banca_IN") && ($_GET['nag'] == ''))
	{ $tipologia = "('IN')";
	  $titolo = 'NUOVI INGRESSI'; 
	  $condizione = "	AND tipologia = 'IN' AND filiale <= 999 ";						
	}


elseif ($_GET['start'] == "banca_OUT") 
	{ $tipologia = "('OUT')";
	  $titolo = 'USCITE'; 
	  $condizione = "	( str_to_date(s.DATA_USCITA,'%d/%m/%Y') >=  str_to_date('01/01/".$annostart."','%d/%m/%Y') )";
	  $filiale    = " AND Filiale_capofila < 999  ";						
	  $esistenza  = " AND socio_istituto != 1 AND NOT EXISTS ";
	  $tipo_data  = "Data Uscita";
	}
*/	
else
	{
		$titolo = " ";
	  $condizione = "	";
	  $tipologia  = " ('IN','OUT') ";

	}

$select = "
					SELECT id, nag, nominativo, tipologia, motivazione, note, filiale, operatore, 
								 data_segnalazione, attivo, data_domanda
				  FROM   tab_motivazioni 
				  WHERE  attivo = 'S'
						".$condizione."
					";
/*
$select = "	
						SELECT
							Filiale_capofila as Filiale,
							idsocio,
							s.nag,
							concat(intestazione_a, ' ', intestazione_b) AS Nominativo,
							case 
							when stato_nag = '0' then 'Potenziale'
							when stato_nag = '1' then 'Attivo'
							when stato_nag = '2' then 'Ex Cliente'
							else '' end as stato_nag,
							ctipmovuscita,
						  data_entrata, 
						  data_uscita
						FROM
							sds_soci AS s
						WHERE
						".$condizione."
						".$filiale."
						".$esistenza."
						(SELECT m.nag, m.motivazione from tab_motivazioni as m
						WHERE tipologia IN ".$tipologia."
						AND m.nag = s.nag)
						ORDER BY Filiale, Nominativo
			";
*/
//echo $select;

logquery ($select);  
$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-warning">Motivazioni '.$titolo.' Soci &nbsp;&nbsp;&nbsp; </h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable"  width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:center; font-size:13px;">Filiale</th>
				<th style="text-align:center; font-size:13px;">CAG</th>
				<th style="text-align:center; font-size:13px;">Nominativo Domanda</th>
				<th style="text-align:center; font-size:13px;">Data Domanda</th>
				<th style="text-align:center; font-size:13px;">Motivazione</th>
				<th style="text-align:center; font-size:13px;">Note</th>
				<th style="text-align:center; font-size:13px;">Data Segnalazione<br>Operatore</th>
			</tr>
		</thead>
		<tbody>
';	

while($dati=mysqli_fetch_array($querydati)){ 

/*
if ( ($_GET['start'] == "IN") OR ($_GET['start'] == "banca_IN") )
	{ $pulsante   = "	<button type='submit' class='btn btn-sm btn-block mb-2' style='background-color:#7195B7;text-align: left;'>
		                    <a href='motivazioni_form.php?action=&start=".$_GET['start']."&codfil=".$dati['Filiale']."&cag=".$dati['nag']."&nominativo=".$dati['Nominativo']."' style='color:white;text-decoration: none;' target='_blank'>
		                    <i class='fas fa-share-square fa-1x text-lightgray-300 col-auto'></i>Inserisci Motivazione
		                    </a>
		                </button> ";
		$tipo_data2 = $dati['data_entrata'];
	}
elseif ( ($_GET['start'] == "OUT") OR ($_GET['start'] == "banca_OUT") )
	{ $pulsante   = "	<button type='submit' class='btn btn-sm btn-block mb-2' style='background-color:#7195B7;text-align: left;'>
		                    <a href='motivazioni_form.php?action=&start=".$_GET['start']."&codfil=".$dati['Filiale']."&cag=".$dati['nag']."&nominativo=".$dati['Nominativo']."' style='color:white;text-decoration: none;' target='_blank'>
		                    <i class='fas fa-share-square fa-1x text-lightgray-300 col-auto'></i>Inserisci Motivazione
		                    </a>
		                </button> ";
		$tipo_data2 = $dati['data_uscita'];
	}
else
	{
	  $pulsante   = $dati['motivazione'];
		$tipo_data2 = '';
	}
*/

echo "	<tr class='table-secondary' style='font-size:13px;'>
			<td align='center'>".$dati['filiale']."</td>
			<td align='center'>".$dati['nag']."</td>
			<td align='left'>".$dati['nominativo']."</td>
			<td align='center'>".$dati['data_domanda']."</td>
			<td align='left'>".$dati['motivazione']."</td>
			<td align='left'>".$dati['note']."</td>
			<td align='center'>".$dati['data_segnalazione']."<br>".$dati['operatore']."</td>
		</tr>";

}

echo '		</tbody>
	</table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';



	echo '<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';


?>