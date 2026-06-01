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

// Mi connetto al database MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
// Mi connetto al database SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Head e CSS
include("css/main.php");
include("css/menu.php");

  if ($_COOKIE['filiale_id'] != 999) {
  echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
      <br><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI CHIANTIBANCA </i></span></h2> </center>';
  echo '<br><br>';    
  }

else 

	{
	// ---------------------------------------------------------------------------------------------------
	// VALORI CONTABILI
	// ---------------------------------------------------------------------------------------------------
	$select_saldi_coge =   "
                       SELECT
                             CG_SALDI.COD_RAPP  ,
                             CG_SALDI.FILIALE  ,
                             CG_SALDI.NUM_RAPP  ,
                             CG_SALDI.SALDO_DIV_CONTO/100 AS SALDO,
                        case CG_SALDI.COD_RAPP
                        when 1770 then (400000 + (CG_SALDI.SALDO_DIV_CONTO/100))
                        else
                             ROUND((CG_SALDI.SALDO_DIV_CONTO/100),0)
                        end AS SALDO2 
                        FROM
                            CG_SALDI  
                        WHERE
                            CG_SALDI.COD_RAPP IN ( 2881,1770,2557 ) 
                        AND
                            CG_SALDI.FILIALE = 990 
                        AND
                            CG_SALDI.NUM_RAPP = 100 
                        ORDER BY
                             SALDO ASC

                    ";

    $result_saldi_coge = odbc_exec($connect, $select_saldi_coge);
    while($dati_saldi_coge = odbc_fetch_object($result_saldi_coge)) {

    	    if ($dati_saldi_coge->COD_RAPP == 2881) 
    	    		{$descrapp = '2881.990.100 - Capitale Sociale'; 
    	    		 $capitale = $dati_saldi_coge->SALDO; }
            if ($dati_saldi_coge->COD_RAPP == 1770) 
            		{$descrapp = '1770.990.100 - Fondo Riacquisto Azioni Proprie'; 
            		 $fondo_saldo = $dati_saldi_coge->SALDO; 
            		 $fondo = 400000+$dati_saldi_coge->SALDO;}
            if ($dati_saldi_coge->COD_RAPP == 2557) 
            		{$descrapp = '2557.990.100 - Quote da Liquidare'; }
	}

	// VALORI
	$plafond 	  = $_GET['plafond_iniziale'];;
	$disp_attuale = $_GET['disponibilita'];
	$disp_senzaliquidaz = $_GET['disp_senzaliquidaz'];
	$fondo 		  = $fondo;

		if ( ($fondo < $disp_attuale) AND ($disp_senzaliquidaz > 0) )
			{
				$limiterimborso = $fondo;
			}
		else
			{
				$limiterimborso = $fondo - ($disp_attuale - $plafond);
				//	$limiterimborso = 0 ;
			}
        
        // echo $fondo;
        // echo 'Limite rimborso '.$limiterimborso;

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


// Calcolo data di partenza
// *****************************************************************************
$date = new DateTime(); 			// empty for now or pass any date string as param
$date->modify('- 6 months');		// 6 mesi indietro da oggi
$AnnoMesedipartenzaMedia = $date->format('Ym');		// formato output AAAAMM

$data_out = date("d/m/Y", strtotime("-1 day"));    // 1 giorno indietro perchè Sadas è a ieri
$data_in  = $date->format('d/m/Y');

// Estrazione dati della posizione in Cessione
// *****************************************************************************
$select = "	select count(*) as qta, sum(Valore_Nominale) as importo from tab_xls_cessionibanca
			where Rimborsato <> 'S' 
			and id2 between 
			(select min(id2) from tab_xls_cessionibanca
			 where  Rimborsato <> 'S' )
			 and (".$_GET['id2']."-1)";
//echo $select;
logquery ($select);
$querydati = mysqli_query($connection, $select);

echo "<center><fieldset style='width:900px;text-align:left;'><br>";
echo '<div class="p-1 mb-2 text-left h5 bg-light text-white">Conteggio relativo alla cessione del Socio
	  </div>
	';
echo '<table width="100%" border="1" cellspacing="1" cellpadding="1" style="background-color:#222222;">';

// colorazione righe
$evidenza  = ' style="background-color:#16903C;" ';
$evidenzab = ' style="background-color:#16903C;font-weight:bold;" ';

	while($datisocio=mysqli_fetch_array($querydati)){ 

	echo '<tr><td colspan="2"><h4>' .$_GET['nominativo'].'</h4></td></tr>';
	echo '<tr><td width="65%">ID2:</td><td>' .$_GET['id2'].'</td></tr>';
	echo '<tr><td>Data Richiesta:</td><td>' .$_GET['dr'].'</td></tr>';

	$vn = floatval($_GET['vn']) ;
	
	echo '<tr><td>Valore nominale richiesto:</td><td><h6>Eur '.number_format($vn, 2, ',' , '.').'</td></tr>';

	// Quantità posizioni in cessione, compresa quella in esame
	echo '<tr><td width="50%" '.$evidenza.'>Quantita\' di posizioni precedenti</td><td '.$evidenzab.'>'.$datisocio['qta']. '</td></tr>';

	// Totale delle cessioni presenti, prima di quella in esame e compresa la stessa
	$importo_totale_cessioni = $datisocio['importo'] + $vn ;  

	echo '<tr><td width="50%" '.$evidenza.'>Importo cessioni precedenti da erogare <i>(comprensiva della richiesta)</i></td><td '.$evidenzab.'>Eur '.number_format($importo_totale_cessioni, 0, ',', '.').'&nbsp;&nbsp;&nbsp;&nbsp;(A)</td></tr>'; 

	echo '<tr><td colspan="2" align="center"><br></td></tr>';

	echo '<tr>
			<td style="background-color:blue;">Plafond disponibile lordo</td>
			<td style="background-color:blue;">Eur ' .number_format($disp_attuale, 0, ',', '.').'&nbsp;&nbsp;&nbsp;&nbsp;(PL)</td></tr>'; 
	echo '<tr>
			<td style="background-color:blue;">Plafond disponibile netto (considerate le liquidazioni da fare)</td>
			<td style="background-color:blue;">Eur ' .number_format($disp_senzaliquidaz, 0, ',', '.').'&nbsp;&nbsp;&nbsp;&nbsp;(PN)</td></tr>'; 
	
	echo '<tr><td colspan="2" align="center"><br></td></tr>';

	echo '<tr>
			<td style="background-color:green;">Valore disponibile da utilizzare <i>(quota Fondo se rientra nel Plafond lordo)</i></td>
			<td style="background-color:green;">Eur ' .number_format($limiterimborso, 0, ',', '.').'&nbsp;&nbsp;&nbsp;&nbsp;(B)</td></tr>'; 
	}

echo '<br><br>';


// Definizione delle date di calcolo
// *****************************************************************************
$date = new DateTime(); 			// empty for now or pass any date string as param
$meseOdierno = $date->format('m');


	// ---------------------------------------------------------------------------------------------------
	// SOMMA ENTRATE DA CAPITALE E FONDO (ultimi 6 mesi)
	// ---------------------------------------------------------------------------------------------------
	$select_somma_entrate =   "
			            SELECT 
			                sum((IMP_DIVISA_CON_SEGNO/100)) AS IMPORTO 
			            FROM CG_MOVIMENTI_CONTABILI 
			            WHERE SEGNO = 'A' 
			            AND COD_RAPP in (2881,1770)
			            AND FILIALE = 990 
			            AND NUM_RAPP = 100 
			            AND DATA_CONT >= '".$data_in."'
			            AND DATA_CONT <= '".$data_out."'
                    ";

    $result_somma_entrate = odbc_exec($connect, $select_somma_entrate);
    while($dati_somma_entrate = odbc_fetch_object($result_somma_entrate)) {

    	    // Totale entrate da Capitale e Fondo ultimi 6 mesi
    	    $somma_entrate = $dati_somma_entrate->IMPORTO; 
	}


// Calcolo valori
// *****************************************************************************

// Previsione su base annua entrate da Capitale e Fondo 
$ipotesiBudgetTotale   = $somma_entrate * 2 ;

// Destino il 90% delle ammissioni al Fondo per i mesi da Settembre a Dicembre
// nei primi 8 mesi invece metto quasi tutto a capitale (sempre che non abbia terminato
// prima le liquidazioni obbligatorie)

$ipotesiBudgetMensile_01_08 = ($ipotesiBudgetTotale - ($ipotesiBudgetTotale * 90 / 100) );
$ipotesiBudgetMensile_10_12 = ($ipotesiBudgetTotale - ($ipotesiBudgetTotale * 10 / 100) );
$BudgetMensile = ( ($ipotesiBudgetMensile_01_08 / 8) + ($ipotesiBudgetMensile_10_12 / 4) / 12 );

$nettoDaPagare 	  	   = $importo_totale_cessioni - $limiterimborso ;
	
$ResiduoInMesi		   = round($nettoDaPagare / $BudgetMensile ) ;

	echo '<tr>
			<td>NETTO DA PAGARE </td>
			<td style="color:#FF0000;">Eur ' .number_format($nettoDaPagare, 0, ',', '.').'&nbsp;&nbsp;&nbsp;&nbsp;(A - B)</td></tr>';  

	echo '<tr>
			<td>Media mese del valore nominale di ammissioni nuovi Soci (media ultimi 6 mesi)</td>
			<td>Eur ' .number_format($BudgetMensile,0,'','.').'
				&nbsp;&nbsp;&nbsp;&nbsp;<i>(ipotesi annua Eur ' .number_format($ipotesiBudgetTotale,0,'','.').')</i>
			</td></tr>';

	echo '<tr>
			<td>Mesi da aggiungere ad oggi </td>
			<td><b>'.$ResiduoInMesi.' mesi </b><small>&nbsp;('.number_format($nettoDaPagare, 0, ',', '.').' : '.number_format($BudgetMensile, 0, ',', '.').')</small></td></tr>';
/*
$date = new DateTime(); 			// empty for now or pass any date string as param
*/
//$date->modify('+ '.$ResiduoInMesi.' months');		// aggiungo i mesi da oggi


if (@$date->modify('+ '.$ResiduoInMesi.' months') == "-0") 
	{$dataipoteticarimborso = 0;}
else {$dataipoteticarimborso = $date->format('m-Y');}

	echo '<tr>
			<td style="background-color:red;"><h4>Ipotesi rientro </h4></td>
			<td style="background-color:red;"><h4>' .$dataipoteticarimborso.'</h4></td></tr>';
	echo '</tr>';

echo '</table></fieldset></center>';


// ********************************************************************************
// CREAZIONE OUTPUT CSV CON TUTTE LE POSIZIONI IN CESSIONE 
// ********************************************************************************
$contenutoOutput = '';
/*
$selectOutput1 = "	select Filiale, nag, nominativo, Data_Richiesta, Valore_Nominale as importo, id , id2,
				 	id2 - (select min(id2) from tab_xls_cessionibanca
				 		 where Rimborsato <> 'S' 
					) as posizioni_precedenti

					from tab_xls_cessionibanca
								where Rimborsato <> 'S'
					ORDER BY id2
					";
*/
$selectOutput1 = "	select Filiale, nag, nominativo, Data_Richiesta, Valore_Nominale as importo, id , id2

					from tab_xls_cessionibanca
								where Rimborsato <> 'S'
					ORDER BY id2
					";

$querydatiOutput1 = mysqli_query($connection, $selectOutput1);

$myfilecessioni = fopen("tmp/cessioni_ipotesirimborso.csv", "w");
$contenutoOutput .= "File aggiornato al ".date("d.m.Y")." - per ricalcolare, passare da singola inquiry su Socio in cessione\n";
$contenutoOutput .= "Filiale;Nag;Nominativo;DataRichiesta;ValoreRichiesto;ID;QtaPosizPreced;ValoreImportoPreced;ValoreAttualeMargine;ValoreNettoPreced;MediaMeseAmmissioni;MesiAggiunti;IpotesiRientro\n";

	$contatore = 0;

	while($datisocioOutput1=mysqli_fetch_array($querydatiOutput1)){ 

	$cont = $contatore++;

		$selectOutput2 = "	select id2, sum(Valore_Nominale) as importo_precedente,
							'".$nettoDaPagare."' as margine,
							(sum(Valore_Nominale) - ".$fondo.") as importo_netto,
							".round($BudgetMensile)." as MediaMeseAmmissioni
							from tab_xls_cessionibanca
								where Rimborsato <> 'S'
								and id2 < ".$datisocioOutput1['id2']."
							order by id2 "; 
							
		$querydatiOutput2 = mysqli_query($connection, $selectOutput2);
		while($datisocioOutput2=mysqli_fetch_array($querydatiOutput2)){ 

			$ResiduoInMesi = round($datisocioOutput2['importo_netto'] / $datisocioOutput2['MediaMeseAmmissioni'] );


	$contenutoOutput .= $datisocioOutput1['Filiale'].";".
						$datisocioOutput1['nag'].";".
						$datisocioOutput1['nominativo'].";".
						$datisocioOutput1['Data_Richiesta'].";".
						$datisocioOutput1['importo'].";".
						$datisocioOutput1['id2'].";".
						$cont .";".
						$datisocioOutput2['importo_precedente'].";".
						number_format($datisocioOutput2['margine'], 0, ',', '.').";".
						$datisocioOutput2['importo_netto'].";".
						$datisocioOutput2['MediaMeseAmmissioni'].";".
						$ResiduoInMesi.";";

			$date = new DateTime(); 			// empty for now or pass any date string as param
			
			if ($ResiduoInMesi > 0)	{$date->modify('+ '.$ResiduoInMesi.' months');	}	// aggiungo i mesi da oggi
			$contenutoOutput .= $date->format('m-Y');
			}

	$contenutoOutput .= "\n";

	}

fwrite($myfilecessioni, $contenutoOutput);
fclose($myfilecessioni);

echo '<br><center>
		<a href="tmp/cessioni_ipotesirimborso.csv" style="text-color:white;" target="_blank">Scarica il tracciato previsionale di TUTTE le cessioni residue</a>
		</center>';


// Tabella Dati a Video
$selectOutput3 = "	select Filiale, nag, nominativo, Note_Motivazioni, 
					Data_Richiesta, 
					Valore_Nominale as importo, id , id2

					from tab_xls_cessionibanca
					where Rimborsato <> 'S'
					and id2 <= ".$_GET['id2']."
					ORDER BY id2
					";

echo '<br><table class="table table-bordered table-hover"  border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td align="left">Filiale</td>
          <td align="left">NAG</td>
          <td align="left">Nominativo</td>
          <td align="left">Note</td>
          <td align="center">Data Richiesta</td>
          <td align="right">Importo</td>
          <td align="right">ID2</td>
        </tr>';

$querydatiOutput3 = mysqli_query($connection, $selectOutput3);
		while($datisocioOutput3=mysqli_fetch_array($querydatiOutput3)){ 

		echo "<tr>
	            <td align='left'>".$datisocioOutput3['Filiale']."</td>
	            <td align='left'>".$datisocioOutput3['nag']."</td>
	            <td align='left'>".$datisocioOutput3['nominativo']."</td>
	            <td align='left'>".$datisocioOutput3['Note_Motivazioni']."</td>
	            <td align='center'>".$datisocioOutput3['Data_Richiesta']."</td>
	            <td align='right'>".number_format(round($datisocioOutput3['importo']),0,',','.')."</td>
	            <td align='right'>".$datisocioOutput3['id2']."</td>
	          </tr>
	        ";
	}

echo '</td></tr></table>';

// *****************************************************
// Fine
}

?>