<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
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
          	"order": [[ 5, "asc" ]],
          	"lengthMenu": [ 20, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   	

<?php

if ($_GET['key'] > 100)
    {$condizionefiliale = '';
    }
    else
     {    
     $condizionefiliale = "AND FILIALE_CAPOFILA = '".$_GET['key']."'";
}


$adesso = date('md');
$adessotitolo = date('d/m');
/*
$select = "	SELECT s.*, p.dataEntrata_origine
			FROM sds_soci as s LEFT JOIN tab_storico_pistoia as p
			ON s.idsocio = p.prot
			WHERE 
			 s.socio_istituto = 1 
			AND s.DATA_ENTRATA like '".$adessotitolo."%' 
			".$condizionefiliale."
			OR p.dataEntrata_origine like '".$adessotitolo."%'
			OR s.DATA_NASCITA like '%".$adesso."'
			ORDER BY s.intestazione_a, s.intestazione_b
			";
*/
$select = "	
			SELECT
			IDSOCIO, NAG, INTESTAZIONE_A, INTESTAZIONE_B, DATA_NASCITA, ETA,
			FILIALE_CAPOFILA, DATA_ENTRATA, STATO_NAG, DATA_ENTRATA_ORIG
			FROM
				sds_soci 
			WHERE
				cast(SOCIO_ISTITUTO as unsigned) = 1
			AND (DATA_ENTRATA LIKE '".$adessotitolo."%' OR DATA_ENTRATA_ORIG LIKE '".$adessotitolo."%')
			".$condizionefiliale."
			UNION
			SELECT
			IDSOCIO, NAG, INTESTAZIONE_A, INTESTAZIONE_B, DATA_NASCITA, ETA,
			FILIALE_CAPOFILA, DATA_ENTRATA, STATO_NAG, DATA_ENTRATA_ORIG
			FROM
				sds_soci 
			WHERE
				cast(SOCIO_ISTITUTO as unsigned) = 1
			AND DATA_NASCITA LIKE '%".$adesso."'
			".$condizionefiliale."
			ORDER BY
				INTESTAZIONE_A,
				INTESTAZIONE_B
			";

logquery ($select);  
$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Oggi '.$adessotitolo.' è il COMPLEANNO di ...<i class="fas fa-birthday-cake fa-1x text-gray-300 col-auto"></i></h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:center; font-size:13px;">IDSOCIO</th>
				<th style="text-align:center; font-size:13px;">NAG</th>
				<th style="text-align:center; font-size:13px;">Nominativo</th>
				<th style="text-align:center; font-size:13px;">Data Nascita</th>
				<th style="text-align:center; font-size:13px;">Età</th>
				<th style="text-align:center; font-size:13px;">Filiale</th>
				<th style="text-align:center; font-size:13px;">Data Entrata</th>
				<th style="text-align:center; font-size:13px;">Anni Socio</th>
				<th style="text-align:center; font-size:13px;">Stato</th>	
				<th style="text-align:center; font-size:13px;">Socio Mutua</th>							
			</tr>
		</thead>
		<tbody>
';	

while($datisocio=mysqli_fetch_array($querydati)){ 

	// Controllo su DATA ENTRATA ORIGINARIA
	if ($datisocio['dataEntrata_origine'] != 0) 
		{$dataEntrata_origine = '<br><small>Data Orig. '.$datisocio['dataEntrata_origine'].'</small>';
		 $etacomplisocio = ( date("Y") - substr($dataEntrata_origine,-4) );}
	else
		{$dataEntrata_origine = '';
		$etacomplisocio = ( date("Y") - substr($datisocio['DATA_ENTRATA'],-4) );}

			// ********************************************************
			// RICERCA SE SONO PRESENTI CESSIONI
			// ********************************************************
			$select_c_c = "	SELECT count(*) as qta
						FROM tab_xls_cessionibanca
						WHERE IDSOCIO = ".$datisocio['IDSOCIO']." 
						AND Rimborsato <> 'S' ";
			$querydati_c_c = mysqli_query($connection, $select_c_c);
			while($dati_c_c=mysqli_fetch_array($querydati_c_c)){ 
				if ($dati_c_c['qta'] == 0) 
					{$count_c = '';} 
				else 
					{$count_c = "&nbsp;<img src='img/ico_cessioni.png' width='12' title='Presenti ".$dati_c_c['qta']." richieste di Cessione a Banca')";}
			}	


			// ********************************************************
			// RICERCA SE IL SOCIO E' A SOFFERENZA
			// ********************************************************
			$select_c_s = "	SELECT count(*) as qta
						FROM tab_xls_esclusioni
						WHERE IDSOCIO = ".$datisocio['IDSOCIO']." 
						AND Escluso_x_Passaggio_a_Sofferenze = 'S'
						AND MovimentoSICRA = 'ID' ";
			$querydati_c_s = mysqli_query($connection, $select_c_s);
			while($dati_c_s=mysqli_fetch_array($querydati_c_s)){ 
				if ($dati_c_s['qta'] == 0) 
					{$count_s = '';} 
				else 
					{$count_s = "&nbsp;<img src='img/ico_sofferenze.png' width='12' title='Posizione esclusa per Sofferenza')";}
			}	

			// ********************************************************
			// RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
			// ********************************************************
			$select_mutua 	  = "	SELECT * FROM TAB_MUTUA
									WHERE CODICEFISCALE = '".$datisocio['CODICE_FISCALE']."'";
			logquery ($select_mutua);  
			$querydati_mutua = mysqli_query($connection, $select_mutua);
				if(mysqli_num_rows($querydati_mutua) > 0)
				    while($datisociomutua = mysqli_fetch_array($querydati_mutua))
				    {
				       $esistenzasociomutua = 
					 	"<i class='fas fa-check fa-2x col-auto' style='color:#9FE2BF;'></i>";
					   $flagsociomutua = 'SI';
					   $modulimutua = 'no';
				    }
				else
				{
				    $esistenzasociomutua = '';
				    $flagsociomutua = 'NO';
				    $modulimutua = 'no';
				}

    // Controllo se è scritta la Data Uscita, se SI la riporto 
	if ($datisocio['DATA_USCITA'] != 0) 
		{$datauscita = "<span style='color:#F76F95;'>".$datisocio['DATA_USCITA']."</span>";}
	else
		{$datauscita = '';}

    // Controllo se è scritta la Data Decesso, se SI la riporto sotto alla Data Uscita
	if ($datisocio['DATA_DECESSO_PF'] != 0) 
		{$datamorte = '<br><small><i class="fas fa-cross fa-1x col-auto" style="color:gray;" title="Data Decesso"></i>'.$datisocio['DATA_DECESSO_PF'].'</small>';}
	else
		{$datamorte = '';}
		
$Nominativo = $datisocio['INTESTAZIONE_A'].' '.$datisocio['INTESTAZIONE_B'];

    // Pallino colorato per avvalorare lo status
	if ( ($datisocio['SOCIO_ANAGRAFE'] == '9') && ($datamorte == '') )
		{$pallino = '<img src="img/ico_pallino_red.png" title="ESTINTO">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

	elseif ( ($datisocio['SOCIO_ANAGRAFE'] == '9') && ($datamorte != '') )	
		{$pallino = '<img src="img/ico_pallino_white.png" title="USCITO PER DECESSO ">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

	else {$pallino = '<img src="img/ico_pallino_green.png" title="In essere">';
			$linksocio = "<a class='text-success' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

    // Controllo se oggi è il suo compleanno, se SI metto l'icona della torta
	if (substr($datisocio['DATA_NASCITA'],4,4) == $adesso) 
		{$bday = '<i class="fas fa-birthday-cake fa-1x text-orange-300 col-auto"  style="color:orange;"></i>';
		 $datinascita = substr($datisocio['DATA_NASCITA'],6,2).'/'.substr($datisocio['DATA_NASCITA'],4,2).'/'.substr($datisocio['DATA_NASCITA'],0,4);
		;}
	else
		{$bday = '';
		 $datinascita = '<span style="color:gray";>'.substr($datisocio['DATA_NASCITA'],6,2).'/'.substr($datisocio['DATA_NASCITA'],4,2).'/'.substr($datisocio['DATA_NASCITA'],0,4).'</span>';
		}

    // Controllo se oggi è il suo compleanno per data entrata, se SI metto l'icona della torta
	if (substr($datisocio['DATA_ENTRATA'],0,5) == $adessotitolo) 
		{$bdayentrata = '<i class="fas fa-birthday-cake fa-1x text-orange-300 col-auto"  style="color:green;"></i>';
		 $dataentrata = $datisocio['DATA_ENTRATA'].$dataEntrata_origine;
		;}
	else
		{$bdayentrata = '';
		 $dataentrata = '<span style="color:gray";>'.$datisocio['DATA_ENTRATA'].$dataEntrata_origine.'</span>';
		}

echo "	<tr class='table-secondary'>
			<td align='center'>".$datisocio['IDSOCIO']."</td>
			<td align='center'>".$datisocio['NAG']."</td>
			<td align='left'><a class='text-success' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$Nominativo."</a></td>
			<td align='center'>".$datinascita.$bday."</td>
			<td align='center'>".$datisocio['ETA']."</td>
			<td align='center'>".$datisocio['FILIALE_CAPOFILA']."</td>
			<td align='center'>".$dataentrata.$bdayentrata."</td>
			<td align='center'>".$etacomplisocio."</td>
			<td align='center'>".$pallino.$count_c.$count_s."</td>
			<td align='center'>".$esistenzasociomutua."</td>
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