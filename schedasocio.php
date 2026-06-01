<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
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
// Connessione a MYSQL
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


if  (empty($_POST['ricerca'])) 
	{

echo "<center>
		<fieldset style='width:600px;text-align:left;'>
		<legend><i class='fas fa-search fa-1x text-gray-300 col-auto'></i> <b>Ricerca Socio ChiantiBanca</legend>";

echo '<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
	<tr>
		<td valign="top" width="75%"><br>
			 <form action="schedasocio2.php" method="POST">
					<input type="text" class="form-control" name="ricerca" id="ricerca" size="40">
					<small style="color:white;"><i>Puoi inserire il cognome/nome - cag - numero Socio - telefono</i></small>
			<br><br>
		</td>
		<td valign="top" align="center" width="25%"><br>
					<button type="submit" class="btn btn-success mb-2">RICERCA</button><br>
			</form>
		</td>
	</tr>	
	</table>';

echo '</fieldset></center>';


}

else

{
?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>
  <script src="js/dataTables.buttons.min.js"></script>
  <script src="js/buttons.print.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
	    	/*
	    	dom: 'Bfrtip'
          	,buttons: [{
                extend: 'print', text: '<i class="fas fa-file-csv fa-2x btn-primary"></i>', 
                className: 'btn btn-dark',
                messageTop: 'Documento ad uso interno - riservato ChiantiBanca',
                messageBottom: 'Documento ad uso interno - riservato ChiantiBanca',
                footer: 'Documento ad uso interno - riservato ChiantiBanca',
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '9pt' )
                        .prepend(
                            '<img src="http://10.119.192.46:8080/soci/img/logo_chiantibanca.png" style="position:absolute; top:0; left:0;opacity:0.2" />'
                        );
 
                    $(win.document.body).find( 'Table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
            },'pageLength']
	    	, */
	    	lengthMenu: [ [ 10, 25, 50, 75, 100, 500, 1000 ], 
	    				  ['10 righe', '25 righe', '50 righe', '75 righe', '100 righe', '500 righe', '1000 righe'] ]
	    	,oLanguage: {"sLengthMenu": "Mostra _MENU_ ", "sSearch": "Cerca nella lista:"}
            ,order: [[ 3, "asc" ]]
          	,deferRender: true
    	} );

	});

	</script>   		

<div id="load" style="display:none;">Loading... Please wait</div>

<?php

$adesso = date('d/m');

if (empty($_POST['filiale'])) 
{$condizionefiliale = "WHERE 
			(( CONCAT(INTESTAZIONE_A,' ',INTESTAZIONE_B) LIKE '%".$_POST['ricerca']."%' OR INTESTAZIONE_RAPPR LIKE '%".$_POST['ricerca']."%' OR VALORE_DATO_CNT LIKE '%".$_POST['ricerca']."%' OR sds_soci.IDSOCIO LIKE '%".$_POST['ricerca']."%' OR sds_soci.NAG LIKE '%".$_POST['ricerca']."%' OR sds_soci.CODICE_FISCALE LIKE '%".$_POST['ricerca']."%'))";}
else 
{$condizionefiliale = " WHERE FILIALE_CAPOFILA = ".$_POST['filiale']." AND SOCIO_ISTITUTO = 1  ";}

// se serve il prodotto CC
if (empty($_POST['conProdottoCC'])) 
{
$condizioneprod1 = "
					, CONCAT(cc.FILIALE_RAPP, '/', cc.NUM_RAPP) AS CONTO,
                      CONCAT(cc.COD_CLASSE, ' - ', cc.DESCRIZIONE) AS PRODOTTOCC
					";
$condizioneprod2 = "
						LEFT JOIN sds_soci_prodotto_cc AS cc 
                            ON sds_soci.COD_RAPP = cc.COD_RAPP
                            AND sds_soci.FILIALE_RAPP = cc.FILIALE_RAPP
                            and sds_soci.NUM_RAPP = cc.NUM_RAPP
					";
}
else 
{
 $condizioneprod1 = "";
 $condizioneprod2 = "";
}

// ********************************************************
// DATI GENERALI
// ********************************************************

$select = "	 	
			SELECT 	
				sds_soci.IDSOCIO,
				FILIALE_CAPOFILA AS FILIALE_ANAGRAFICA,
				sds_soci.NAG,
				CONCAT(INTESTAZIONE_A, ' ', INTESTAZIONE_B) AS INTESTAZIONE,
				DATA_NASCITA AS DATA_NASCITA,
				ETA,
				DATA_ENTRATA,
				CASE WHEN TIPO_DATO_CNT = 'TEL' THEN VALORE_DATO_CNT END AS TELE,
				CASE WHEN TIPO_DATO_CNT = 'CELL' THEN VALORE_DATO_CNT END AS CELL,
				CASE WHEN TIPO_DATO_CNT = 'MAIL' THEN VALORE_DATO_CNT END AS MAIL,
				CASE WHEN TIPO_DATO_CNT = 'PEC' THEN VALORE_DATO_CNT END AS PEC,
				SOCIO_ISTITUTO AS SOCIO_ANAGRAFE,
				DATA_DECESSO AS DATA_DECESSO,
				NAG_RAPPR AS NAG_RAPP,
				INTESTAZIONE_RAPPR AS RAPPRESENTANTE,
				NUMERO_AZIONI AS NUM_AZIONI,
				(NUMERO_AZIONI * 30.33) as VAL_AZIONI,
				DATA_USCITA,
				tab_storico_pistoia.dataEntrata_origine,
				CODICE_FISCALE,
				CTIPMOVUSCITA,
				SETTORISTA
				".$condizioneprod1."
			FROM
				sds_soci 
				LEFT JOIN sds_soci_certificati ON (sds_soci.IDSOCIO = sds_soci_certificati.IDSOCIO)
				LEFT JOIN sds_soci_daticontatto ON (sds_soci.NAG = sds_soci_daticontatto.NAG)
				LEFT JOIN tab_storico_pistoia ON  (sds_soci.IDSOCIO = tab_storico_pistoia.prot)
				".$condizioneprod2."
			".$condizionefiliale."
			group by INTESTAZIONE, DATA_NASCITA, sds_soci.IDSOCIO, sds_soci.NAG, FILIALE_CAPOFILA
			ORDER BY 1";

logquery ($select);
// echo $select;
$querydati = mysqli_query($connection, $select);
//$result = odbc_exec($connect, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Soci ChiantiBanca</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:left; font-size:13px;">Nr.Socio</th>
				<th style="text-align:left; font-size:13px;">Filiale</th>
				<th style="text-align:left; font-size:13px;">NAG</th>
				<th style="text-align:left; font-size:13px;">Nominativo</th>
				<th style="text-align:center; font-size:13px;">Data Nascita</th>
				<th style="text-align:center; font-size:13px;">Età</th>
				<th style="text-align:center; font-size:13px;">Data Entrata</th>
				<th style="text-align:center; font-size:13px;">Data Uscita</th>
				<th style="text-align:center; font-size:13px;">Telefono</th>
				<th style="text-align:center; font-size:13px;">Num.Az.</th>
				<th style="text-align:center; font-size:13px;">Stato</th>
				<th style="text-align:center; font-size:13px;">Moduli</th>
				<th style="text-align:center; font-size:13px;">Mutua</th>
			</tr>
		</thead>
		<tbody>
';	

	// ------------------------
	// Creo file CSV
	// ------------------------
	if (empty($_POST['filiale'])) 
		{$codfiliale = "";}
	else 
		{$codfiliale = $_POST['filiale'];}
	$contenutofile = '';
    $myfile = fopen("tmp/listasoci".$codfiliale.".csv", "w");
    $contenutofile .= "Socio;Filiale;NAG;Nominativo;DataNascita;Eta;DataEntrata;DataUscita;DataDecessoPF;NuMTelefono;NumAzioni;Stato;SocioMutua;Settorista;Conto;ProdottoCC\n";


while($datisocio=mysqli_fetch_array($querydati)){ 
//while($datisocio = odbc_fetch_object($result)) {

	if (empty($_POST['conProdottoCC'])) 
	{
	 	$conProdottoCC = '<br><small style="color:gray;">'.$datisocio['CONTO'].' '.$datisocio['PRODOTTOCC'].'</small>';
	}
	else
	{
		$conProdottoCC = '';
	}

			// ********************************************************
			// RICERCA SE SONO PRESENTI MONITOR
			// ********************************************************
			$select_c_m = "	SELECT count(*) as qta
						FROM tab_monitor_soci
						WHERE cag = ".$datisocio['NAG']." 
						AND attivo = 'S' ";
			$querydati_c_m = mysqli_query($connection, $select_c_m);
			while($dati_c_m=mysqli_fetch_array($querydati_c_m)){ 
				if ($dati_c_m['qta'] == 0) 
					{$count_m = '';} 
				else 
					{$count_m = "&nbsp;<img src='img/ico_occhiogiallo.png' width='12' title='Presenti ".$dati_c_m['qta']." evidenze in Monitor')";}
			}	

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
						AND MovimentoSicra = 'ID' "; 
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
					   /*
					   $esistenzasociomutua = 
					 	"<a href='mutua_schedasocio.php?cag=".$datisociomutua['cag']."' title='Apri Scheda Socio Mutua'>
					 	    <i class='fas fa-check fa-2x col-auto' style='color:#9FE2BF;'></i></a>
					 	 
					 	 <a href='modulistica_mutua.php?prot=".$datisocio['IDSOCIO']."&cag=".$datisocio['NAG']."&socio=".urlencode(stripslashes($datisocio['INTESTAZIONE']))."&idsocio=".$datisocio['IDSOCIO']."' title='Modelli precompilati MUTUA ".$datisocio['INTESTAZIONE']."'><i class='fas fa-file-signature fa-2x col-auto' style='color:#9FE2BF;'></i></a>";
					   $modulimutua = 'si';
					   */
				    }
				else
				{
				    $esistenzasociomutua = '';
				    $flagsociomutua = 'NO';
				    $modulimutua = 'no';
				}

			// ********************************************************
			// RICERCA DEL NOME DELLA FILIALE
			// ********************************************************
			$select_filiale	  = "	SELECT * FROM tab_psw
									WHERE filiale = ".$datisocio['FILIALE_ANAGRAFICA'];
			logquery ($select_filiale);  
			$querydati_filiale = mysqli_query($connection, $select_filiale);
				if(mysqli_num_rows($querydati_filiale) > 0)
				    while($datifiliale = mysqli_fetch_array($querydati_filiale))
				    {
				        $nomefiliale = $datifiliale['desc_filiale'];
				    }
				else
				{
				    $nomefiliale = '';
				}


			// ********************************************************
			// Decodifico il SETTORISTA 
			// ********************************************************
		    	$querysettorista = 	"SELECT *
		    						 FROM 	tab_dipendenti
		    					 	 WHERE  settorista = '".$datisocio['SETTORISTA']."'" ;
		    		 
		    	$querydatisettorista = mysqli_query($connection, $querysettorista);
		        if(mysqli_num_rows($querydatisettorista) > 0) {
		            while($datisettorista = mysqli_fetch_array($querydatisettorista))  {
		            
		                $nomesettorista = $datisettorista['dipendente'];
		                $mansionesettorista = $datisettorista['mansionewprof'];
		            }
		                
		        } 
		        else
		        {
		            $nomesettorista = '';
		            $mansionesettorista = '';
		        }

    // Controllo se è scritta la Data Uscita, se SI la riporto 
	if ($datisocio['DATA_USCITA'] != 0) 
		{$datauscita = "<span style='color:#F76F95;'>".$datisocio['DATA_USCITA']."</span>";}
	else
		{$datauscita = '';}

    // Controllo se è scritta la Data Decesso, se SI la riporto sotto alla Data Uscita
	if ($datisocio['DATA_DECESSO'] != 0) 
		{$datamorte = '<br><small><i class="fas fa-cross fa-1x col-auto" style="color:gray;" title="Data Decesso"></i>'.$datisocio['DATA_DECESSO'].'</small>';}
	else
		{$datamorte = '';}

    // Controllo se oggi è il suo compleanno, se SI metto l'icona della torta
	if (substr($datisocio['DATA_NASCITA'],0,5) == $adesso) 
		{$bday = '<i class="fas fa-birthday-cake fa-1x text-gray-300 col-auto"  style="color:orange;"></i>';}
	else
		{$bday = '';}

	// Controllo su DATA ENTRATA ORIGINARIA
	if ($datisocio['dataEntrata_origine'] != 0) 
		{$dataEntrata_origine = '<br><small>Data Orig. '.$datisocio['dataEntrata_origine'].'</small>';}
	else
		{$dataEntrata_origine = '';}

    // Controllo se oggi è il suo compliSocio, ovvero il compleanno dalla data di ingresso...se SI metto l'icona della torta
	$dataEntrata = $datisocio['DATA_ENTRATA']; 
	
	if (substr($dataEntrata,0,5) == $adesso) 
		{$complisocio = '<i class="fas fa-birthday-cake fa-1x col-auto" style="color:orange;" title="CompliSocio"></i>';}
	else
		{$complisocio = '';}

    // Pallino colorato per avvalorare lo status
	/*
	if ( ($datisocio['SOCIO_ANAGRAFE'] == '9') && ($datamorte == '') )
		{$pallino = '<img src="img/ico_pallino_red.png" title="ESTINTO">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

	elseif ( ($datisocio['SOCIO_ANAGRAFE'] == '9') && ($datamorte != '') )	
		{$pallino = '<img src="img/ico_pallino_white.png" title="USCITO PER DECESSO ">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 
	*/

	if ( $datisocio['CTIPMOVUSCITA'] == 'ES' ) 
		{$pallino = '<img src="img/ico_pallino_red.png" title="USCITO per ESCLUSIONE">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

	elseif ( ($datisocio['CTIPMOVUSCITA'] == '  ') &&  ($datisocio['DATA_USCITA'] != 0) ) 
		{$pallino = '<img src="img/ico_pallino_red.png" title="USCITO">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

	elseif ( $datisocio['CTIPMOVUSCITA'] == 'RE' ) 
		{$pallino = '<img src="img/ico_pallino_red.png" title="USCITO per RECESSO">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";}  

	elseif ( $datisocio['CTIPMOVUSCITA'] == 'MO' ) 
		{$pallino = '<img src="img/ico_pallino_white.png" title="USCITO per DECESSO">';
		$linksocio = "<a class='text-light' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";}  

	else {$pallino = '<img src="img/ico_pallino_green.png" title="In essere">';
			$linksocio = "<a class='text-success' href='sqldati_schedasocio.php?id=".$datisocio['IDSOCIO']."'>".$datisocio['INTESTAZIONE']."</a>";} 

    //echo var_dump($datisocio['int1Delegato']) ;
    if (empty($datisocio['RAPPRESENTANTE'])) 
        {$rappresentante = '';}
        else 
        {$rappresentante = '<br><small style="color:#43F5C6;">'.$datisocio['RAPPRESENTANTE'].'</small>'; }

    $datinascita = substr($datisocio['DATA_NASCITA'],6,2).'/'.substr($datisocio['DATA_NASCITA'],4,2).'/'.substr($datisocio['DATA_NASCITA'],0,4);
    
	echo "	  <tr class='table-secondary'>   
				<td style='text-align:left;'>".$datisocio['IDSOCIO']."</td>
				<td style='text-align:left;' title='".$nomefiliale."'>".$datisocio['FILIALE_ANAGRAFICA']."</td>
				<td style='text-align:left;'>".$datisocio['NAG']."</td>
				<td style='text-align:left;'>".$linksocio.$rappresentante.$conProdottoCC."</td>
				<td style='text-align:center;'>".$datinascita."<br>".$bday."</td>
			    <td style='text-align:center;'>".$datisocio['ETA']."</td>
				<td style='text-align:center;'>".$datisocio['DATA_ENTRATA']." ".$complisocio." ".$dataEntrata_origine."</td>
				<td style='text-align:center;'>".$datauscita.$datamorte."</td>
				<td style='text-align:center;'>".$datisocio['CELL']."<br>".$datisocio['TELE']."</td>
				<td style='text-align:center;'>".number_format($datisocio['NUM_AZIONI'],0,',','.')."<br><small>&euro;&nbsp;".number_format($datisocio['VAL_AZIONI'],2,',','.')."</small>&nbsp;</td>
				<td style='text-align:center;'>".$pallino.$count_c.$count_s.$count_m."</td>";

if ( ($datisocio['SOCIO_ANAGRAFE'] == 'Socio') OR ($datauscita == "") ) {
	echo '	<td style="text-align:center; font-size:11px; text-decoration: none;"><a href="modulistica.php?prot='.$datisocio['IDSOCIO'].'&cag='.$datisocio['NAG'].'&socio='.urlencode(stripslashes($datisocio['INTESTAZIONE'])).'&idsocio='.$datisocio['IDSOCIO'].'&mutua=&user='.'"><i class="fas fa-file-signature fa-2x text-gray-300 col-auto" title="Modelli precompilati '.$datisocio['INTESTAZIONE'].'"></i></a></td>';
} else { 
    echo '	<td style="text-align:center; font-size:11px; text-decoration: none;"></td>';
}    
	echo '	<td style="text-align:center;font-size:11px; text-decoration: none;">'.$esistenzasociomutua.'</td>';
	 		  
	echo '  </tr>'; 


	// SCRIVO IL CSV
    $contenutofile .= $datisocio['IDSOCIO'].";".$datisocio['FILIALE_ANAGRAFICA'].";".$datisocio['NAG'].";".$datisocio['INTESTAZIONE'].";".$datisocio['DATA_NASCITA'].";".$datisocio['ETA'].";".$datisocio['DATA_ENTRATA'].";".$datisocio['DATA_USCITA'].";".$datisocio['DATA_DECESSO'].";".$datisocio['CELL'].";".number_format($datisocio['NUM_AZIONI'],0,',','.').";".$datisocio['SOCIO_ANAGRAFE'].";".$flagsociomutua.";".$nomesettorista.";".$datisocio['CONTO'].";".$datisocio['PRODOTTOCC']."\n";

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

}

    echo "";

?>


<center><a href="tmp/listasoci<?php echo $codfiliale;?>.csv"><img src="img/google_docs.png"></a>&nbsp;<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
