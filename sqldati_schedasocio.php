<?php
// Ufficio Processi
// 03/05/2024 #MZ Intervento per tracciatura Alba
// 05/01/2025 #LF Fix lughezza LN
function logAlbaNAG($NAG){
	// Check utente loggato
	$user = "";
	if (!isset($_COOKIE['usr_id'])){
		header('Location: https://chiantibanca.worktogether.it/login.asp?ReturnUrl=https%3A%2F%2Fchiantibanca.worktogether.it%2Fviews%2Fjump.asp%3Ftype%3DApp%26AppId%3D14');
		exit;
	} else {
		// $user = 'LN00'.$_COOKIE['usr_id'];
		$user = 'LN' . str_pad($_COOKIE['usr_id'], 5, '0', STR_PAD_LEFT); // #LF 05/01/2025 Fix lughezza LN
	}
	// Avvia tracciatura
	require_once("./lib/loggerALBA.php");
	$logger = New loggerALBA("soci","schedasocio",$user);
	$logger->setMode("");
	$logger->NAG($NAG);
	$logger->flush();
}

// *****************************************************************************
// Portale Soci
// Sviluppo e realizzazione: Alessio Fedi (2022)
// v.Sicra
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Head e CSS
include("css/main.php");
include("css/menu.php");

if (get_browser_name($_SERVER['HTTP_USER_AGENT']) == "Internet Explorer")
	{$imgext = "jpg";}
else
	{$imgext = "png";}

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

// echo '<div id="load" style="display:none;">Loading... Please wait</div>';

echo '<center>';

$adesso = date('d/m');
$adessobday = date('md');

// ********************************************************
// DATI SOCIO
// ********************************************************

$select = "	SELECT 
				sds_soci.IDSOCIO,
				sds_soci.NAG,
				concat (sds_soci.INTESTAZIONE_A, ' ', sds_soci.INTESTAZIONE_B) as INTESTAZIONE,
				sds_soci.CODICE_FISCALE,
				sds_soci.PARTITA_IVA,
				sds_soci.TIPO_NAG,
				sds_soci.TIPO_SOGGETTO,
				sds_soci.SESSO,
				sds_soci.FILIALE_CAPOFILA,
				sds_soci.STATO_NAG,
				sds_soci.SOCIO_ISTITUTO,
				sds_soci.DATA_NASCITA,
				sds_soci.ETA,
				sds_soci.DATA_DECESSO,
				sds_soci.SETTORISTA,
				sds_soci.SETTORE,
				sds_soci.RAMO,
				sds_soci.PROF_ATTIVITA,
				sds_soci.SEGMENTO_CLIENTE,
				sds_soci.PA_3 as PIAZZA,
				sds_anag_piazze.DESCR_PIAZZA,
				sds_soci.DATA_USCITA,
				sds_soci.DATA_ENTRATA,
				sds_soci.DIRITTO_DI_VOTO,
				sds_soci.CTIPMOVUSCITA,
				sds_soci_certificati.NUMERO_AZIONI as AZIONI,
				sds_soci_certificati.VALORE_AZIONI as VALORE_NOMINALE,
				sds_soci.COD_RAPP,
				sds_soci.FILIALE_RAPP,
				sds_soci.NUM_RAPP,
				sds_soci.IDSOCIO_SUB,
				sds_soci.ACQUISTO_PERIOD,
				sds_soci.NAZIONI_PERIOD,
				sds_soci.DATA_FINEPACK_PERIOD,
				sds_soci.VIA_RES,
				sds_soci.CAP_RES,
				sds_soci.DESCR_COM_RES,
				sds_soci.LOCALITA_RES,
				sds_soci.PROVINCIA_RES,
				sds_soci.INTESTAZ_CORR,
				sds_soci.VIA_CORR,
				sds_soci.CAP_CORR,
				sds_soci.DESCR_COM_CORR,
				sds_soci.LOCALITA_CORR,
				sds_soci.PROVINCIA_CORR,
				sds_soci.TEL,
				sds_soci.CELL,
				sds_soci.MAIL,
				sds_soci.PEC,
				sds_soci.NAG_RAPPR,
				sds_soci.INTESTAZIONE_RAPPR as RAPPRESENTANTE,
				sds_soci.CODICE_FISCALE_RAPPR,
				sds_soci.DATA_NASCITA_RAPPR,
				sds_soci.TEL_RAPPR
			FROM 
				sds_soci 
				left join sds_soci_certificati			ON sds_soci.IDSOCIO = sds_soci_certificati.IDSOCIO
				left join sds_anag_piazze				ON sds_soci.PA_3    = sds_anag_piazze.PA_3
			WHERE sds_soci.IDSOCIO = '".$_GET['id']."'
			GROUP BY sds_soci.IDSOCIO 
			";

logquery ($select); 

$querydati = mysqli_query($connection, $select);

while($datisocio=mysqli_fetch_array($querydati)){ 

		if (substr($datisocio['DATA_NASCITA'],5,4) == $adessobday) 
		{$bday = '<i class="fas fa-birthday-cake fa-1x text-gray-300 col-auto"></i>';}
	else
		{$bday = '';}

			// ********************************************************
		    // Controllo se oggi è il suo compliSocio, ovvero il compleanno dalla data di ingresso...
			// se SI metto l'icona della torta arancione
			// ********************************************************
			$dataEntrata = $datisocio['DATA_ENTRATA']; 
			
			if (substr($dataEntrata,0,5) == $adesso) 
				{$complisocio = '<i class="fas fa-birthday-cake fa-1x col-auto" style="color:orange;" title="CompliSocio"></i>';}
			else
				{$complisocio = '';}
					

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
					 	"<img src='img/mutua_logo_bianco_full.png' width='120'>";
					   $modulimutua = 'no';
					   /*
				       $esistenzasociomutua = 
					 	"<a href='mutua_schedasocio.php?cag=".$datisociomutua['cag']."' title='Apri Scheda Socio Mutua'><img src='img/mutua_logo_bianco_full.png' width='150'></a>";
					   $modulimutua = 'si';
					   */
				    }
				else
				{
				    $esistenzasociomutua = '';
				    $modulimutua = 'no';
				}

			// ********************************************************
			// RICERCA DEL NOME DELLA FILIALE
			// ********************************************************
			$select_filiale	  = "	SELECT * FROM tab_psw
									WHERE filiale = ".$datisocio['FILIALE_CAPOFILA'];
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
			// RICERCA MOTIVO INGRESSO o USCITA
			// ******************************************************** 
			$select_inout	  = "SELECT s.idsocio, t.ctipomov as TipoMovimento, tm.xtipomov, t.data_delibera, t.xmotivo
								FROM sds_soci s, sds_soci_movinout t, sds_soci_tipomovimento tm
								WHERE s.IDSOCIO = '".$_GET['id']."'
								AND s.idsocio = t.idsocio
								AND t.ctipomov = tm.ctipomov ";  
			logquery ($select_inout);  
			$querydati_inout = mysqli_query($connection, $select_inout);
				if(mysqli_num_rows($querydati_inout) > 0)
				    while($dati_inout = mysqli_fetch_array($querydati_inout))
				    {

				   		//******** FARE TANTE IF PER LE CASISTICHE DI INGRESSI E USCITE 
				   		//E RIPORTARE LE VARIABILI DA USARE SOTTO

				        $motivouscita = $dati_inout['TipoMovimento'];
				    }
				else
				{
				    $motivouscita = '';
				}

$nominativo = $datisocio['INTESTAZIONE'];
$cag = $datisocio['NAG'];

// Ufficio Processi
// 20240503 #mz Intervento per tracciatura Alba
logAlbaNAG($cag);

$ValoreNominale = number_format($datisocio['VALORE_NOMINALE'],2,',','.');


	// -------------------------------------------------------
	// SCHEDA ANAGRAFICA
	// -------------------------------------------------------
	echo "<fieldset style='width:1100px;text-align:left;'>
			<!-- <legend style='color:#00478C;'>Dati Socio</legend> -->";
	// Titolo con link alla generazione del QRCODE dell'indirizzo
	// $chl = $datisocio['codFil'].";MG0672;P;".$nominativo.";".$datisocio['indirSpedIndirizzo'].';'.$datisocio['indirSpedCAP'].' '.$datisocio['indirSpedLocalita'].' '.$datisocio['indirSpedProvincia'].';'.$datisocio['indirizzoPEC'];
	/*echo '<div class="p-1 mb-2 text-left h5 bg-light text-white">DATI SOCIO &nbsp;&nbsp; 
	        <a style="text-decoration:none;color:#999999" href="https://chart.googleapis.com/chart?cht=qr&chs=120x120&chl='.$chl.'" target="_blank">
	        QRCODE
	        </a>
	     </div>'; */

	if ($datisocio['SESSO'] == 'M') {
	    $icona = 'ico_man.png';
	    $datinascita = substr($datisocio['DATA_NASCITA'],6,2).'/'.substr($datisocio['DATA_NASCITA'],4,2).'/'.substr($datisocio['DATA_NASCITA'],0,4). ' ('.$datisocio['ETA'].' anni)';
	    $nagrappr = '';

		    if ($datisocio['DATA_DECESSO'] == 0) {
		    	$datadecesso = '';
		    }
		    else
		    {
		    	$datadecesso = substr($datisocio['DATA_DECESSO'],6,2).'/'.substr($datisocio['DATA_DECESSO'],4,2).'/'.substr($datisocio['DATA_DECESSO'],0,4);
		    }

		    if ($datisocio['DATA_USCITA'] == 0) {
		    	$datauscita = '';
		    }
		    else
		    {
		    	$datauscita = $datisocio['DATA_USCITA'];
		    }
	}
	elseif ($datisocio['SESSO'] == 'F') {
	    $icona = 'ico_woman.png';
	    $datinascita = substr($datisocio['DATA_NASCITA'],6,2).'/'.substr($datisocio['DATA_NASCITA'],4,2).'/'.substr($datisocio['DATA_NASCITA'],0,4). ' ('.$datisocio['ETA'].' anni)';
	    $nagrappr = '';

		    if ($datisocio['DATA_DECESSO'] == 0) {
		    	$datadecesso = '';
		    }
		    else
		    {
		    	$datadecesso = substr($datisocio['DATA_DECESSO'],6,2).'/'.substr($datisocio['DATA_DECESSO'],4,2).'/'.substr($datisocio['DATA_DECESSO'],0,4);
		    }

		    if ($datisocio['DATA_USCITA'] == 0) {
		    	$datauscita = '';
		    }
		    else
		    {
		    	$datauscita = $datisocio['DATA_USCITA'];
		    }
	}
	else 
	{
		$icona = 'ico_azienda.png';
		$datinascita = '';
		$nagrappr = $datisocio['NAG_RAPPR'];
		$datadecesso = '';

		    if ($datisocio['DATA_USCITA'] == 0) {
		    	$datauscita = '';
		    }
		    else
		    {
		    	$datauscita = $datisocio['DATA_USCITA'];
		    }
	}

	if ( ($datisocio['SOCIO_ISTITUTO'] != '1') && ($datadecesso == '') ) {$pallino = '<img src="img/ico_pallino_red.png" title="ESTINTO">';} 
	elseif ( ($datisocio['SOCIO_ISTITUTO'] != '1') && ($datadecesso != '') ) {$pallino = '<img src="img/ico_pallino_white.png" title="USCITO PER DECESSO)">';} 
	else {$pallino = '<img src="img/ico_pallino_green.png" title="In essere">';} 


	if ($datisocio['SEGMENTO_CLIENTE'] == 18)
		{	$dipendente = 'DIPENDENTE CHIANTIBANCA';}
	else
		{	$dipendente = '';}

	echo '<table width="100%" border="0" style="background-color:#222222;">';
	echo '<tr height="20"><td colspan="3" width="90%" align="left"><b style="font-size:18px;">' .$nominativo.' '.$pallino.' '.$bday.$complisocio.'</b></td>
			<td align="right">
			<a style="color:black;" href="modulistica.php?prot='.$datisocio['IDSOCIO'].'&cag='.$datisocio['NAG'].'&socio='.stripslashes($nominativo).'&idsocio='.$datisocio['IDSOCIO'].'&mutua=&user=soci"><img src="img/edit.png" width="30"></a>
			<a href="modulistica.php?prot='.$datisocio['IDSOCIO'].'&cag='.$datisocio['NAG'].'&socio='.stripslashes($nominativo).'&idsocio='.$datisocio['IDSOCIO'].'&mutua=&soci=XXX">
			<!-- <i class="fas fa-file-signature fa-2x text-gray-300 col-auto" style="color:blue;" title="Modelli precompilati '.$nominativo.'"></i></a>--></td>
			<td align="right" valign="top" rowspan="20" width="150">
			<img src="img/'.$icona.'" width="100"><br><br>'.$esistenzasociomutua.'<br><br>'.$dipendente.'
			</td></tr>';
	echo '<tr height="20">
	            <td width="15%" style="color:#A0B7CE;">Numero Socio</td><td width="25%" style="font-size:14px;">'.$datisocio['IDSOCIO']. '</td>
	            <td width="15%" style="color:#A0B7CE;">Socio dal</td><td width="25%">'.$datisocio['DATA_ENTRATA']. '</td>
	       </tr>';
	echo '<tr height="20">
	            <td width="15%" style="color:#A0B7CE;">NAG</td><td width="25%" style="font-size:14px;">'.$datisocio['NAG']. '</td>
	            <td width="15%" style="color:#A0B7CE;">Azioni possedute</td><td width="25%" style="font-size:14px;">nr.<b>'.$datisocio['AZIONI']. '</b> &nbsp;&nbsp;(Eur '.$ValoreNominale.') </td>
	       </tr>';
	echo '<tr height="20"><td width="15%" style="color:#A0B7CE;">Filiale</td><td>'.$datisocio['FILIALE_CAPOFILA'].'&nbsp;'.$nomefiliale.'</td><td width="15%" style="color:#A0B7CE;">Nome Delegato</td><td width="25%" style="font-size:14px;">'.$datisocio['RAPPRESENTANTE']. '</td></tr>';
	echo '<tr height="20"><td width="15%" style="color:#A0B7CE;">Piazza</td><td>'.$datisocio['PIAZZA'].'&nbsp;'.$datisocio['DESCR_PIAZZA'].'</td><td width="15%" style="color:#A0B7CE;">NAG Delegato</td><td width="25%" style="font-size:14px;">'.$nagrappr. '</td></tr>';
	echo '<tr height="20"><td width="15%" style="color:#A0B7CE;">Conto Corrente</td><td>'.$datisocio['FILIALE_RAPP'].'-'.$datisocio['NUM_RAPP'].'</td>
						  <td width="15%" style="color:#A0B7CE;">Diritto di Voto dal</td><td>'.$datisocio['DIRITTO_DI_VOTO'].'</td></tr>';

	// Decodifico il SETTORISTA 
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
    

    // Controllo la presenza di PACK AZIONI
	if ($datisocio['ACQUISTO_PERIOD'] == 'Y')
		{	$packtitolo = 'Presenza PACK';
			$packdescr  = 'nr.'.$datisocio['NAZIONI_PERIOD'].' azioni/mese fino al '.$datisocio['DATA_FINEPACK_PERIOD'];
		}
	else
		{	$packtitolo = '';
			$packdescr = '';
	    }

	echo '<tr height="20"><td width="15%" style="color:#A0B7CE;">Settorista</td><td>'.$datisocio['SETTORISTA'].' - <span title="'.$mansionesettorista.' ">'.$nomesettorista.'</span></td>
						  <td width="15%" style="color:#A0B7CE;">'.$packtitolo.'</td><td>'.$packdescr.'</td></tr>';

	// echo '<tr><td width="15%" style="border-bottom:dotted 1px;color:white;"></td><td colspan="3" style="border-bottom:dotted 1px;color:white;"><br></td></tr>'; // riga separazione

	// echo '<tr><td width="15%" style="color:#A0B7CE;">Luogo nascita/cost.</td><td colspan="3">XXXXXXXX</td>';

	echo '<tr><td width="15%"><br></td><td><br></td></tr>';
	echo '<tr><td width="15%" style="background-color:#A0B7CE;color:white;">Dati Anagrafici</td><td></td></tr>';
	echo '<tr><td width="15%" style="color:#A0B7CE;">Data nascita</td><td>'.$datinascita.'</td>
				<td width="15%" style="color:#A0B7CE;">Codice Fiscale</td><td>'.$datisocio['CODICE_FISCALE'].'</td></tr>';

    // Indirizzo di RESIDENZA
 	echo '<tr><td width="15%" style="color:#A0B7CE;">Indirizzo Residenza</td><td colspan="3">'.$datisocio['VIA_RES']. ' - '.$datisocio['CAP_RES']. ' - '.$datisocio['DESCR_COM_RES']. ' - '.$datisocio['PROVINCIA_RES']. '&nbsp;
		<a href="https://www.google.com/maps/search/?api=1&query='.$datisocio['VIA_RES'].'+'.$datisocio['CAP_RES'].'+'.$datisocio['DESCR_COM_RES'].'+'.$datisocio['PROVINCIA_RES'].'" target="_blank">
		<i class="fas fa-map-marker-alt fa-1x text-gray-300 col-auto" alt="Vedi in Google Maps" title="Vedi in Google Maps"></i></a>';
		
	// Se presente una nota per la zona di competenza la riporto
    	$queryzona = 	"SELECT *
    					 FROM 	tab_comuni_soci_note
    					 WHERE  cag = ".$datisocio['NAG'] ;
    		 
    	$querydatizona = mysqli_query($connection, $queryzona);
        if(mysqli_num_rows($querydatizona) > 0) 
        while($datisociozona = mysqli_fetch_array($querydatizona))
            {    
           
            if ($datisociozona['status_esito'] == 'Valido')
                {$notezona = "ico_occhioverde.png"; }
            elseif ($datisociozona['status_esito'] == 'Escludere')
                {$notezona = "ico_occhiorosso.png"; }
            else {$notezona = "ico_occhiogiallo.png"; }

            $datinota = "Annotazione su requisito competenza territoriale: &#10;"
                        ."ID ".$datisociozona['id']." - OPER " .$datisociozona['operatore']. " - Data ".$datisociozona['data_segnalazione']." &#10; &#10;"
                        .$datisociozona['note']
                        ;

            echo '&nbsp;
            <img src="img/'.$notezona.'" width="20" title="'.$datinota.'">
            ';

            }
 
    echo '</td></tr>'; 

    // Indirizzo di CORRISPONDNZA
	echo '<tr><td width="15%" style="color:#A0B7CE;">Indirizzo Spedizione</td><td colspan="3">'.$datisocio['VIA_CORR']. ' - '.$datisocio['CAP_CORR']. ' - '.$datisocio['DESCR_COM_CORR']. ' - '.$datisocio['PROVINCIA_CORR']. '&nbsp;
		<a href="https://www.google.com/maps/search/?api=1&query='.$datisocio['VIA_CORR'].'+'.$datisocio['CAP_CORR'].'+'.$datisocio['DESCR_COM_CORR'].'+'.$datisocio['PROVINCIA_CORR'].'" target="_blank">
		<i class="fas fa-map-marker-alt fa-1x text-gray-300 col-auto" alt="Vedi in Google Maps" title="Vedi in Google Maps"></i>
		</a>
		</td></tr>'; 
		
	// Tipo di spedizione

    	$querytiposped = 	"SELECT
								 s.NAG as NAG,
								 case 
								 when r.TIPO_SPEDIZIONE = 'P' then 'Posta'
								 when r.TIPO_SPEDIZIONE = 'H' then 'Relax Banking'
								 when r.TIPO_SPEDIZIONE = 'K' then 'PEC'
								 else '' end as TIPO_SPEDIZIONE
							FROM
								SOCI_ANAGRAFICA as s, ANAG_RAPPORTI as r 
							WHERE s.NAG = 	".$datisocio['NAG'] ."
							AND
								s.NUM_RAPP = r.NUM_RAPP
							AND
								s.FILIALE_RAPP = r.FILIALE  
							AND
								s.COD_RAPP = r.COD_RAPP 
							";
    		 
    	$querydatitiposped = odbc_exec($connect, $querytiposped);
        if(odbc_num_rows($querydatitiposped) > 0) 
        while($datitiposped = odbc_fetch_object($querydatitiposped))
            {   

            	if ($datitiposped->TIPO_SPEDIZIONE == 'PEC') 
            		{$alert_invio = '<i class="fas fa-exclamation-triangle fa-1x text-yellow-300 col-auto" style="color:yellow;" title="Prestare attenzione, invio tramite PEC esclusivo"></i>';}
            	else
            		{$alert_invio = '';}

				echo '<tr><td width="15%" style="color:#A0B7CE;">Tipo Spedizione</td><td colspan="3">'.$datitiposped->TIPO_SPEDIZIONE.' '.$alert_invio.'</td></tr>';
			}

	// riga separazione
	//echo '<tr><td width="15%" style="border-bottom:dotted 1px;color:white;"></td><td colspan="3" style="border-bottom:dotted 1px;color:white;"><br></td></tr>'; 

	echo '<tr><td width="15%"><br></td><td><br></td></tr>';
	// Dati di CONTATTO
	echo '<tr><td width="15%" style="background-color:#A0B7CE;color:white;">Dati Contatto</td><td></td>
		<td style="background-color:#A0B7CE;color:white;">Iscrizione Eventi</td>
		<td>&nbsp;&nbsp;&nbsp;
		';

		// ESTRAZIONE EVENTI OVE IL SOCIO E' ISCRITTO
    	$queryeventi = 	"SELECT  count(*) as qta
		                FROM tab_eventi_iscrizioni 
		                WHERE 
		                      NAG = ".$datisocio['NAG'];
    		 
    	$querydatieventi = mysqli_query($connection, $queryeventi);
        while($datisocioeventi = mysqli_fetch_array($querydatieventi))
            {    
            	if ($datisocioeventi['qta'] == 0)
            		{echo 'Nessuna iscrizione ad eventi';}
            	else {echo 'Iscritto a <a style="text-decoration:none;color:white;" href="eventi_iscrizioni_nag.php?action=elenco&nag='.$datisocio['NAG'].'">'.$datisocioeventi['qta'].' eventi</a></td></tr>';}
			}

	echo '</td></tr>';

    	$querycontatti = 	"SELECT NAG, PROCEDURA, 
    						 CASE TIPO_DATO_CNT 
										WHEN 'TEL' THEN 'Telefono' 
    						 			WHEN 'CELL' THEN 'Cellulare'
    						 			WHEN 'MAIL' THEN 'Email'
    						 			WHEN 'PEC' THEN 'PEC'
    						 			ELSE '' 
    						 END as TIPO,
    						 VALORE_DATO_CNT as DATO, NOTE
    					 	 FROM 	sds_soci_daticontatto
    					 	 WHERE  nag = ".$datisocio['NAG'] ;
    		 
    	$querydaticontatti = mysqli_query($connection, $querycontatti);
        if(mysqli_num_rows($querydaticontatti) > 0) 
        while($datisociocontatti = mysqli_fetch_array($querydaticontatti))
            {    
				echo '<tr><td width="15%" style="color:#A0B7CE;" title="Fonte '.$datisociocontatti['PROCEDURA'].'">'.$datisociocontatti['TIPO'].'</td><td colspan="3">'.$datisociocontatti['DATO'].'</td></tr>';
			}

	echo '<tr><td width="15%"><br></td><td><br></td></tr>';
	// Dati RAMO / SETTORE / ATTIVITA'
	echo '<tr><td width="15%" style="background-color:#A0B7CE;color:white;">Dati Ramo/Settore/Attivita\'</td><td></td></tr>';

	$querymerceologico = 	"
							SELECT  
							 RIFERIMENTO,
							 TIPO,
							 DESCRIZIONE 
								 FROM 	sds_soci, sds_soci_merceologico 
								 WHERE  RIFERIMENTO = 'Ramo'
								 AND	sds_soci.ramo = sds_soci_merceologico.TIPO
								 AND	nag = ".$datisocio['NAG']."
							UNION
							SELECT  
							 RIFERIMENTO,
							 TIPO,
							 DESCRIZIONE 
								 FROM 	sds_soci, sds_soci_merceologico 
								 WHERE  RIFERIMENTO = 'Settore'
								 AND	sds_soci.settore = sds_soci_merceologico.TIPO
								 AND	nag = ".$datisocio['NAG']."
							UNION
							SELECT  
							 RIFERIMENTO,
							 TIPO,
							 DESCRIZIONE 
								 FROM 	sds_soci, sds_soci_merceologico 
								 WHERE  RIFERIMENTO = 'Professione'
								 AND	sds_soci.PROF_ATTIVITA = sds_soci_merceologico.TIPO
								 AND    TIPO >= 100
								 AND	nag = ".$datisocio['NAG']."
							 ORDER BY RIFERIMENTO" ;
       		 
    	$querydatimerceologico = mysqli_query($connection, $querymerceologico);
        while($datisociomerceologico = mysqli_fetch_array($querydatimerceologico))
            {    
				echo '<tr><td width="15%" style="color:#A0B7CE;">'.$datisociomerceologico['RIFERIMENTO'].'</td><td colspan="3">'.$datisociomerceologico['TIPO'].' '.$datisociomerceologico['DESCRIZIONE'].'</td></tr>';
			}

	echo '<tr><td width="15%"><br></td><td><br></td></tr>';
	// Dati GESTIONALI
	echo '<tr><td width="15%" style="background-color:#A0B7CE;color:white;">Dati Gestionali</td><td></td></tr>';

	// ESTRAGGO LE DATE DI DELIBERA
    	$querydelibera = 	"SELECT *
    					 	 FROM 	sds_soci_movinout
    					 	 WHERE  idsocio = ".$datisocio['IDSOCIO'] ; 
    	$querydatidelibera = mysqli_query($connection, $querydelibera); 
        if(mysqli_num_rows($querydatidelibera) > 0) 
        while($datisociodelibera = mysqli_fetch_array($querydatidelibera))
            {    
            	if ($datisociodelibera['CTIPOMOV'] == 'AM') {$ammissione = ' Ammissione <br>CDA del '.$datisociodelibera['DATA_DELIBERA'];} else {$ammissione = '';}
            	if ($datisociodelibera['CTIPOMOV'] == 'ID') {$iniziodecadenza = ' <br>Inizio Decadenza dal '.$datisociodelibera['DATA_DELIBERA'];} else {$iniziodecadenza = '';}
            	if ($datisociodelibera['CTIPOMOV'] == 'RE') {$recesso = ' Uscita per Recesso <br>CDA del '.$datisociodelibera['DATA_DELIBERA'];} else {$recesso = '';}
            	if ($datisociodelibera['CTIPOMOV'] == 'ES') {$esclusione = ' Uscita per Esclusione <br>CDA del '.$datisociodelibera['DATA_DELIBERA'];} else {$esclusione = '';}
            	if ($datisociodelibera['CTIPOMOV'] == 'MO') {$morte = ' Uscita per Morte <br>CDA del '.$datisociodelibera['DATA_DELIBERA'];} else {$morte = '';}
            }


	// decodifica STATO_NAG e SOCIO_ISTITUTO
	if ($datisocio['SOCIO_ISTITUTO'] == '1')  { $statosocio = 'Socio a capitale'; $coloresocio = 'lightgreen';} 
	else { $statosocio = 'Ex Socio'; $coloresocio = 'red';} 
	
	if ($datisocio['STATO_NAG'] == '0')  { $statonag = 'Cliente Potenziale'; $colorenag = 'lightyellow';} 
		elseif ($datisocio['STATO_NAG'] == '1') { $statonag = 'Cliente con rapporti'; $colorenag = 'lightgreen';} 
		else   { $statonag = 'Ex Cliente'; $colorenag = 'red';} 


	echo '<tr>
	            <td width="15%" style="color:#A0B7CE;">Entrato il</td><td>'.$datisocio['DATA_ENTRATA'].$ammissione.'</td>
	            <td width="15%" style="color:#A0B7CE;">Uscito il</td><td style="color:red;">'.$datauscita.'&nbsp; '.$iniziodecadenza.$recesso.$esclusione.$morte.'</td>
	        </tr>';
	echo '<tr>
	            <td width="15%" style="color:#A0B7CE;">Status Socio</td><td style="color:'.$coloresocio.';">'.$statosocio.'</td>
	            <td width="15%" style="color:#A0B7CE;">Deceduto il</td><td>'.$datadecesso.'</td>
	        </tr>';


	echo '<tr>
	            <td width="15%" style="color:#A0B7CE;">Status NAG</td><td style="color:'.$colorenag.';">'.$statonag.'</td>
	            <td width="15%" style="color:#A0B7CE;"></td><td></td>
	            
	        </tr>';


	// CHIUDO WHILE GENERALE (datisocio)
	}
	

?>

</table>

<?php

	// riga separazione
	// echo '<tr><td width="15%" style="border-bottom:dotted 1px;color:white;"></td><td colspan="3" style="border-bottom:dotted 1px;color:white;"><br></td></tr>'; 


			// CONTEGGIO MONITOR
			$select_m0 = "	SELECT count(*) as qta FROM tab_monitor_soci WHERE cag = ".$cag." AND attivo = 'S' ";
			$querydati_m0 = mysqli_query($connection, $select_m0);
			while($dati_m0 = mysqli_fetch_array($querydati_m0))		{ if ($dati_m0['qta'] == 0) {$count_m0 = '';} else {$count_m0 = "background-color:#E9B96E;color:black;";}  }

			// CONTEGGIO DOMANDE
			$select_c1 = "	SELECT count(*) as qta FROM SDS_SOCI_DOMANDE WHERE NAG = ".$cag." ";
			$querydati_c1 = mysqli_query($connection, $select_c1);
			while($dati_c1 = mysqli_fetch_array($querydati_c1))		{ if ($dati_c1['qta'] == 0) {$count_c1 = '';} else {$count_c1 = "background-color:#343434;";}  }

			// CONTEGGIO DOCUMENTI
			$select_c2 = "	SELECT count(*) as qta FROM ISIDOC_CONTRATTI WHERE NAG = ".$cag." AND COD_CONTRATTO = 'SOCICN02' ";
			$querydati_c2 = odbc_exec($connect, $select_c2);
			while($dati_c2 = odbc_fetch_object($querydati_c2))		
				{ $docs = $dati_c2->QTA ; }

			// Documenti personali per decessi
			$select_c2b = "	SELECT count(*) as qta FROM ISIDOC_DOCUMENTI_PERSONALE WHERE NAG = ".$cag." AND COD_TIPO_DOCUMENTO IN 
						('DI000006TP000006',
						 'DI000006TP000003',
						 'DI000006TP000004',
						 'DI000006TP000007',
						 'DI000006TP000001'
						)"; 
			$querydati_c2b = odbc_exec($connect, $select_c2b);
			while($dati_c2b = odbc_fetch_object($querydati_c2b))		
				{ $docs_pers = $dati_c2b->QTA ; }

			$totale_docs = $docs + $docs_pers;

			if ($totale_docs > 0) 
				{$count_c2 = "background-color:#343434;";} 
			else {$count_c2 = "";}

			// CONTEGGIO TRASFERIMENTI
			$select_c3 = "	SELECT COUNT(*) as qta
	                        FROM
	                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_02 INNER JOIN SOCI_MOVIMENTI AS SOCI_MOVIMENTI_01  ON (SOCI_ANAGRAFICA_02.IDSOCIO = SOCI_MOVIMENTI_01.CSOCIO_TRASF ) ,
	                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_02 INNER JOIN ANAG_NAG AS ANAG_NAG_02  ON (SOCI_ANAGRAFICA_02.NAG = ANAG_NAG_02.NAG ) ,
	                              SOCI_MOVIMENTI  AS SOCI_MOVIMENTI_01 INNER JOIN SOCI_ANAGRAFICA AS SOCI_ANAGRAFICA_01  ON (SOCI_MOVIMENTI_01.IDSOCIO = SOCI_ANAGRAFICA_01.IDSOCIO ) ,
	                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_01 INNER JOIN ANAG_NAG AS ANAG_NAG_01  ON (SOCI_ANAGRAFICA_01.NAG = ANAG_NAG_01.NAG )  
	                        WHERE
	                              SOCI_MOVIMENTI_01.CTIPOMOV IN ( 'TR','CO','FU','DO','SU' ) 
	                        AND
	                              SOCI_MOVIMENTI_01.IDSOCIO =  ".$_GET['id']."
	                        OR
	                        	  SOCI_MOVIMENTI_01.CSOCIO_TRASF =  ".$_GET['id']." "; 
			$querydati_c3 = odbc_exec($connect, $select_c3);
			while($dati_c3 = odbc_fetch_object($querydati_c3))		{ if ($dati_c3->QTA == 0) {$count_c3 = '';} else {$count_c3 = "background-color:#343434;";}  }
      	
			// CONTEGGIO CESSIONI A BANCA
			$select_c4 = "	SELECT count(*) as qta, Rimborsato FROM tab_xls_cessionibanca 
							WHERE IDSOCIO = ".$_GET['id']." 
							GROUP BY Rimborsato"; 
			$querydati_c4 = mysqli_query($connection, $select_c4);
			$rowcount_c4=mysqli_num_rows($querydati_c4);

			  if ($rowcount_c4 == 0) {$count_c4 = "";} 
			  else 
			  {
				while($dati_c4 = mysqli_fetch_array($querydati_c4))		
					{ 
					  if ($dati_c4['Rimborsato'] <> 'S' AND ($dati_c4['qta'] == 0)) 
							{$count_c4 = '';}
					  elseif ($dati_c4['Rimborsato'] == 'S' AND ($dati_c4['qta'] <> 0)) 
					  		{$count_c4 = 'background-color:#343434;';}
					  else  {$count_c4 = "background-color:#6F6F6F;";}  
					}
				}

			// CONTEGGIO ESCLUSIONI
			$select_c5 = "	SELECT count(*) as qta, MovimentoSicra FROM tab_xls_esclusioni 
							WHERE MovimentoSicra in ('ID','RL','ES') 
							AND IDSOCIO = ".$_GET['id']." 
							GROUP BY MovimentoSicra"; 
			$querydati_c5 = mysqli_query($connection, $select_c5);
			$rowcount_c5=mysqli_num_rows($querydati_c5);

			  if ($rowcount_c5 == 0) {$count_c5 = "";} 
			  else 
			  {

			while($dati_c5 = mysqli_fetch_array($querydati_c5))		
					{ 
					  if ($dati_c5['MovimentoSicra'] == 'ES' AND ($dati_c5['qta'] <> 0)) 
					  		{$count_c5 = 'background-color:#343434;';}
					  else  {$count_c5 = "background-color:#6F6F6F;";}  
					}
				}


			// CONTEGGIO DECESSI 
			$select_c6 = "	SELECT count(*) as qta
							FROM tab_xls_decessi_eredi_storico e
							WHERE e.NAG = ".$cag."
							UNION
							SELECT count(*) as qta
							FROM sds_soci_movinout m
							left join sds_soci s on m.idsocio = s.idsocio 
							left join tab_xls_decessi_eredi_storico e on e.nag = s.NAG
							WHERE s.NAG = ".$cag."
							AND m.CTIPOMOV in ('MO','RS','RL')  "; 
			$querydati_c6 = mysqli_query($connection, $select_c6);
			while($dati_c6 = mysqli_fetch_array($querydati_c6))		{ if ($dati_c6['qta'] == 0) {$count_c6 = '';} else {$count_c6 = "background-color:#6F6F6F;";}  }

			/*
			// CONTEGGIO PAC PRESENTI
			$select_c_p = "	SELECT count(*) as qta
						FROM tab_xls_ammissioni
						WHERE CAG = ".$cag." 
						AND Pac = 'S' ";
			$querydati_c_p = mysqli_query($connection, $select_c_p);
			while($dati_c_p=mysqli_fetch_array($querydati_c_p)){ 
				if ($dati_c_p['qta'] == 0) {$count_p = '';} else {$count_p = "<span style='color:yellow;'>&nbsp;&nbsp;(".$dati_c_p['qta'].")</span>";}
			}
			*/
?>



<br>
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" style="text-decoration:none;" data-toggle="tab" href="#Info">Info</a>
  </li>    
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_m0; ?>" data-toggle="tab" href="#Monitor">Monitor</a>
  </li>    
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_c1; ?>" data-toggle="tab" href="#Domande">Domande</a>
  </li>    
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_c2; ?>" data-toggle="tab" href="#Documenti">Documenti</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_c3; ?>" data-toggle="tab" href="#cessionisocio">Trasferimenti</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_c4; ?>" data-toggle="tab" href="#cessionibanca">Cessioni a Banca</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_c5; ?>" data-toggle="tab" href="#esclusioni">Esclusioni</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" style="text-decoration:none;<?php echo $count_c6; ?>" data-toggle="tab" href="#decessi">Decessi</a>
  </li>
</ul>

<div id="myTabContent" class="tab-content">

  <div class="tab-pane fade active show" id="Info">
  	<br><small style="color:#6F6F6F;">
  		Le linguette colorate contengono informazioni.<br>
  		</small>
  </div>


  <div class="tab-pane fade" id="Monitor">

  	<?php 
  		// 
  		// MONITOR
  		//
			$select_mon = "	SELECT * FROM tab_monitor_soci
							WHERE cag = ".$cag." AND attivo = 'S'
							ORDER BY STR_TO_DATE(data_ricezione, '%d/%m/%Y') DESC
						";

			// echo $select_mon;

			$querydati_mon = mysqli_query($connection, $select_mon);

			echo "<br>
			<a href='/soci/monitor_new.php?tipo=new&cag=".$cag."&nominativo=".$nominativo."'>Nuova Segnalazione</a>
			<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
						<td align='center'>View</td>
						<td align='left'>Ricezione</td>
						<td align='left'>Tipologia</td>
						<td align='left'>Data Ricezione</td>
						<td align='left'>Ricevuto da</td>
						<td align='left'>Esito</td>
						<td align='left'>Segnalato a</td>
						<td align='left'>Data Segnalazione</td>
					</tr>";

			if(mysqli_num_rows($querydati_mon) > 0) {

			while($dati_mon = mysqli_fetch_array($querydati_mon)){ 

/*
			if ($dati_mon['forma_ricezione'] == 'Mail') 
				{$ico_riservato = '<i class="fas fa-eye-slash fa-1x text-gray-300 col-auto" title="Riservato"></i>';}
			else // pubblico 
				{$ico_riservato = '<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Pubblico"></i>';}
*/

			if ($dati_mon['status_esito'] == 'ChiusoPositivo') { $icostatus = '<img src="img/ico_pallino_green.png" title="Positivo">'; }
			if ($dati_mon['status_esito'] == 'ChiusoNegativo') { $icostatus = '<img src="img/ico_pallino_red.png" title="Negativo">'; }
			if ($dati_mon['status_esito'] == 'InSospeso') { $icostatus = '<img src="img/ico_pallino_yellow.png" title="In Sospeso">'; }
			if ($dati_mon['status_esito'] == '') { $icostatus = '<img src="img/ico_pallino_yellow.png" title="">'; }

			$link_mon = '
			<a href="monitor_lista.php?id='.$dati_mon['id'].'&nominativo='.$nominativo.'&cag='.$cag.'"><i class="fas fa-desktop fa-1x text-gray-300 col-auto"></i></a>';

			echo "	<tr>
						<td align='center'>".$link_mon."</td>
						<td align='left'>".$dati_mon['forma_ricezione']."</td>
						<td align='left'>".$dati_mon['tipologia']."</td>
						<td align='left'>".$dati_mon['data_ricezione']."</td>
						<td align='left'>".$dati_mon['amezzodi']."</td>
						<td align='left'>".$icostatus."</td>
						<td align='left'>".$dati_mon['segnalato_a']."</td>
						<td align='left'>".$dati_mon['data_segnalazione']."</td>
					</tr>
				";

			}

			}
			echo '</table>'; 
		?>

  </div>

  <div class="tab-pane fade" id="Domande">

  	<?php 
  		// 
  		// DOMANDE SOCIO
  		//
			$select_dom = "	SELECT *
						FROM SDS_SOCI_DOMANDE
						WHERE NAG = ".$cag."
						ORDER BY STR_TO_DATE(DATA_DOMANDA, '%d/%m/%Y'), CTIPODOM
						";

			//logquery ($select);  
			//echo $select_dom;

			$querydati_dom = mysqli_query($connection, $select_dom);

			echo "<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
						<td align='center'>Data Domanda</td>
						<td align='center'>Data CDA</td>
						<td align='center'>Tipo</td>
						<td align='center'>Nr<br>Azioni</td>
						<td align='center'>Soglia</td>
						<td align='center'>Subentrante</td>
						<td align='center'>Trasferimento<br>da Socio</td>
						<td align='center'>Defunto</td>
						<td align='center'>Data Operaz</td>
						<td align='center'>Data Accoglim</td>
					</tr>";

			if(mysqli_num_rows($querydati_dom) > 0) {

			while($dati_dom=mysqli_fetch_array($querydati_dom)){ 

			$decodifica_tipo = 'DA = Domanda Ammissione &#10;'
							  .'DD = Domanda ..... &#10;'
							  .'DL = Domanda Rimborso &#10;'
							  .'DQ = Domanda Ulteriori Quote &#10;'
							  .'DR = Domanda Recesso &#10;'
							  .'DS = Domanda Subentro Erede &#10;'
							  .'DT = Domanda Trasferimento &#10;' ;

			$decodifica_soglia = '0 = ..... &#10;'
							  .'1 = Trasferimenti/Subentri &#10;'
							  .'2 = Standard 33 azioni &#10;'
							  .'3 = Rateizzazione Under30 3x11 &#10;'
							  .'4 = Rateizzazione ChiantiMutua 8x2 &#10;'
								;

			$decodifica_dataoperazione = substr($dati_dom['DATA_OPERAZIONE'],6,2).'/'.substr($dati_dom['DATA_OPERAZIONE'],4,2).'/'.substr($dati_dom['DATA_OPERAZIONE'],0,4);
			$decodifica_dataaccoglimento = substr($dati_dom['DATA_ACCOGLIMENTO'],6,2).'/'.substr($dati_dom['DATA_ACCOGLIMENTO'],4,2).'/'.substr($dati_dom['DATA_ACCOGLIMENTO'],0,4);

			echo "	<tr>
						<td align='center'>".$dati_dom['DATA_DOMANDA']."</td>
						<td align='center'>".$dati_dom['DATA_DELIBERA']."</td>
						<td align='center' title='".$decodifica_tipo."'>".$dati_dom['CTIPODOM']."</td>
						<td align='center'>".$dati_dom['NAZIONI']."</td>
						<td align='center' title='".$decodifica_soglia."'>".$dati_dom['SOGLIA']."</td>
						<td align='center'><a style='text-decoration:none;color:lightblue;' href='sqldati_schedasocio.php?id=".$dati_dom['SUBENTRANTE_IDSOCIO']."' target='_blank'>".$dati_dom['SUBENTRANTE_IDSOCIO']."</a></td>
						<td align='center'><a style='text-decoration:none;color:lightblue;' href='sqldati_schedasocio.php?id=".$dati_dom['TRASFERIMENTO_DA_IDSOCIO']."' target='_blank'>".$dati_dom['TRASFERIMENTO_DA_IDSOCIO']."</a></td>
						<td align='center'><a style='text-decoration:none;color:lightblue;' href='sqldati_schedasocio.php?id=".$dati_dom['DEFUNTO_IDSOCIO']."' target='_blank'>".$dati_dom['DEFUNTO_IDSOCIO']."</a></td>
						<td align='center'>".$decodifica_dataoperazione."&nbsp;&nbsp;<small>".$dati_dom['ORA_OPERAZIONE']."</small></td>
						<td align='center'>".$decodifica_dataaccoglimento."&nbsp;&nbsp;<small>".$dati_dom['ORA_ACCOGLIMENTO']."</small></td>
					</tr>
				";

			//echo	"</tr>";

			}

			}
			echo '</table>'; 
		?>

  </div>
 
  <div class="tab-pane fade" id="Documenti">

  	<?php 
  		// 
  		// DOCUMENTI ISIDOC SOCI
  		//
			$select_isi = "	SELECT *
						FROM SDS_SOCI_ISIDOC
						WHERE NAG = ".$cag."
						ORDER BY STR_TO_DATE(DATA_DOCUMENTO, '%d/%m/%Y'), DESCR_DOCUMENTO
						";

			//logquery ($select);  
			//echo $select_isi;

			$querydati_isi = mysqli_query($connection, $select_isi);

			echo "<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
						<td align='center'>Cod Contratto</td>
						<td align='center'>Documento</td>
						<td align='center'>Data Documento</td>
						<td align='center'>Data Acquisizione</td>
						<td align='center'>Note Contratto</td>
						<td align='center'>Presenza PDF</td>
					</tr>";

			if(mysqli_num_rows($querydati_isi) > 0) {

			while($dati_isi=mysqli_fetch_array($querydati_isi)){ 

			// $decodifica_dataoperazione = substr($dati_dom['DATA_OPERAZIONE'],6,2).'/'.substr($dati_dom['DATA_OPERAZIONE'],4,2).'/'.substr($dati_dom['DATA_OPERAZIONE'],0,4);
			// $decodifica_dataaccoglimento = substr($dati_dom['DATA_ACCOGLIMENTO'],6,2).'/'.substr($dati_dom['DATA_ACCOGLIMENTO'],4,2).'/'.substr($dati_dom['DATA_ACCOGLIMENTO'],0,4);

			IF (substr($dati_isi['DESCR_DOCUMENTO'],0,10) == 'SOCICN02: ') {$descr_documento = substr($dati_isi['DESCR_DOCUMENTO'],10,30); } else {$descr_documento = $dati_isi['DESCR_DOCUMENTO']; }
//						<td align='center'>".$dati_isi['DESCR_DOCUMENTO']."</td>

			echo "	<tr>
						<td align='center'>".$dati_isi['COD_CONTRATTO']."</td>
						<td align='center'>".$descr_documento."</td>
						<td align='center'>".$dati_isi['DATA_DOCUMENTO']."</td>
						<td align='center'>".$dati_isi['DATA_ACQUISIZIONE']."</td>
						<td align='center'>".$dati_isi['NOTE_CONTRATTO']."</td>
						<td align='center'>".$dati_isi['PRESENZA_DOCUMENTO']."</td>
					</tr>
				";

				}			
			}

				  // Estrazione dei documenti personali inerenti il defunto
      		$select_docs =   "
						SELECT 
							NAG ,
							COD_TIPO_DOCUMENTO,
							DESCR_TIPO_DOCUMENTO,
							DATA_DOCUMENTO,
							DATA_ACQUISIZIONE,
							PRESENZA_DOCUMENTO AS PDF,
							COD_USER_INS AS MATRICOLA,
							PRESENZA_NOTE AS NOTE
						FROM ISIDOC_DOCUMENTI_PERSONALE 
						WHERE COD_TIPO_DOCUMENTO IN 
						('DI000006TP000006',
						 'DI000006TP000003',
						 'DI000006TP000004',
						 'DI000006TP000007',
						 'DI000006TP000001'
						)
						AND NAG = ".$cag."
                        "; 

      		$result_docs = odbc_exec($connect, $select_docs);

      		if (odbc_num_rows($result_docs) > 0) {
	          while ($dati_docs = odbc_fetch_object($result_docs)) {

				echo "	<tr>
							<td align='center'>DOC.PERSONALI</td>
							<td align='center'>".$dati_docs->DESCR_TIPO_DOCUMENTO."</td>
							<td align='center'>".$dati_docs->DATA_DOCUMENTO."</td>
							<td align='center'>".$dati_docs->DATA_ACQUISIZIONE."</td>
							<td align='center'></td>
							<td align='center'>".$dati_docs->PDF."</td>
						</tr>";

				}
			}





			
			echo '</table>'; 
		?>

  </div>

    <div class="tab-pane fade" id="cessionibanca">

		<?php
  		// 
  		// CESSIONI A BANCA
  		//
			$select_c = "	SELECT *
						FROM tab_xls_cessionibanca
						WHERE IDSOCIO = ".$_GET['id']."
						ORDER BY STR_TO_DATE(Data_Richiesta, '%d/%m/%Y') 
						";

			//logquery ($select);  
			//echo $select_c;

			$querydati_c = mysqli_query($connection, $select_c);

			echo "<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
						<td align='center'>Data Richiesta</td>
						<td align='center'>Data CDA</td>
						<td align='center'>Tipo</td>
						<td align='center'>Nr<br>Azioni</td>
						<td align='center'>Valore Azioni</td>
						<td align='center'>Rimborsato</td>
						<td align='center'>Data Rimborso</td>
						<td align='center'>Data Pagamento</td>
						<td align='left'>Note</td>
						<td align='right'>ID</td>
					</tr>";

			while($dati_c=mysqli_fetch_array($querydati_c)){ 

            if ($dati_c['Rimborsato'] == 'S') 
                {
                	// $tdstyle = 'title ="QUOTE CEDUTE o LIQUIDATE" style="background-color:green;" ';
        			$link_ID = "<td align='right'><a style='text-decoration:none;color:white;' >".$dati_c['ID']."</a></td>";
        		}
                else
                {
                	// $tdstyle = 'title ="QUOTE ANCORA A CAPITALE" style="background-color:red;" ';
        			$link_ID = "<td align='right'><a style='text-decoration:none;color:white;' href='stats/situazione_plafond.php?nominativo=".urlencode($nominativo)."&id=".$dati_c['ID']."&id2=".$dati_c['ID2']."&dr=".$dati_c['Data_Richiesta']."&vn=".$dati_c['Valore_Nominale']."' target='_blank'>".$dati_c['ID']."</a></td>";
        		}

			echo "	<tr>
						<td align='center'>".$dati_c['Data_Richiesta']."</td>
						<td align='center'>".$dati_c['CDA']."</td>
						<td align='center' title='P parziale - T totale'>".$dati_c['Totale_Parziale']."</td>
						<td align='center'>".$dati_c['Numero_Azioni']."</td>
						<td align='center'>".$dati_c['Valore_Nominale']."</td>
						<td align='center'>".$dati_c['Rimborsato']."</td>
						<td align='center'>".$dati_c['Data_Rimborso']."</td>
						<td align='center'>".$dati_c['Data_Pagamento']."</td>
						<td align='left'>".$dati_c['Note_Motivazioni']."<br>".$dati_c['Pagamento']."</td>";

			echo $link_ID;

			echo	"</tr>";

			}
			echo '</table>';
		?>

  </div>

   <div class="tab-pane fade" id="cessionisocio">

		<?php
  		// 
  		// CESSIONI A SOCIO
  		//
     		 $select_c =   "
                        SELECT
                               SOCI_ANAGRAFICA_01.NAG AS NAG_TRASFERENTE ,
                               SOCI_MOVIMENTI_01.IDSOCIO AS IDSOCIO_TRASFERENTE ,
                               ANAG_NAG_01.INTESTAZIONE_A + ' ' + ANAG_NAG_01.INTESTAZIONE_B AS SOCIO_TRASFERENTE,
                               SOCI_ANAGRAFICA_02.NAG AS NAG_RICEVENTE ,
                               SOCI_MOVIMENTI_01.CSOCIO_TRASF AS IDSOCIO_RICEVENTE,
                               ANAG_NAG_02.INTESTAZIONE_A + ' ' + ANAG_NAG_02.INTESTAZIONE_B AS SOCIO_RICEVENTE,
                               SOCI_MOVIMENTI_01.DATA_MOVIMENTO AS DATA_MOVIMENTO ,
                               abs(SOCI_MOVIMENTI_01.IMPORTO / 30.33)  as AZIONI,
                               abs(SOCI_MOVIMENTI_01.IMPORTO) AS IMPORTO ,
                               abs(SOCI_MOVIMENTI_01.ISOVRAPPREZZO) AS ISOVRAPPREZZO ,
                               SOCI_MOVIMENTI_01.CTIPOMOV AS CTIPOMOV
                        FROM
                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_02 INNER JOIN SOCI_MOVIMENTI AS SOCI_MOVIMENTI_01  ON (SOCI_ANAGRAFICA_02.IDSOCIO = SOCI_MOVIMENTI_01.CSOCIO_TRASF ) ,
                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_02 INNER JOIN ANAG_NAG AS ANAG_NAG_02  ON (SOCI_ANAGRAFICA_02.NAG = ANAG_NAG_02.NAG ) ,
                              SOCI_MOVIMENTI  AS SOCI_MOVIMENTI_01 INNER JOIN SOCI_ANAGRAFICA AS SOCI_ANAGRAFICA_01  ON (SOCI_MOVIMENTI_01.IDSOCIO = SOCI_ANAGRAFICA_01.IDSOCIO ) ,
                              SOCI_ANAGRAFICA  AS SOCI_ANAGRAFICA_01 INNER JOIN ANAG_NAG AS ANAG_NAG_01  ON (SOCI_ANAGRAFICA_01.NAG = ANAG_NAG_01.NAG )  
                        WHERE
                              SOCI_MOVIMENTI_01.CTIPOMOV IN ( 'TR','CO','FU','DO','SU','VE' ) 
                        AND
                              SOCI_MOVIMENTI_01.IDSOCIO =  ".$_GET['id']."
                        OR
                        	  SOCI_MOVIMENTI_01.CSOCIO_TRASF =  ".$_GET['id']." 
                        ORDER BY SOCI_MOVIMENTI_01.DATA_MOVIMENTO
                        "; 

			//logquery ($select_c);  
			//echo $select_c;
			echo "<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
			          <td align='left'><small>NAG Trasf</td>
			          <td align='left'><small>IDSocio Trasf</td>
			          <td align='left'><small>Socio Trasferente</td>
			          <td align='left'><small>NAG Ricev</td>
			          <td align='left'><small>IDSocio Ricev</td>
			          <td align='left'><small>Socio Ricevente</td>
			          <td align='left'><small>Data Movimento</td>
			          <td align='left'><small>Tipo Movimento</td>
			          <td align='left'><small>Azioni</td>
			          <td align='left'><small>Importo</td>
					</tr>";


      		$result_c = odbc_exec($connect, $select_c);
      		while($dati_c = odbc_fetch_object($result_c)) {

            $linksocio_trasf = "<a class='text-red-light' style='text-decoration:none;color:lightblue;' href='sqldati_schedasocio.php?id=".$dati_c->IDSOCIO_TRASFERENTE."'>".$dati_c->SOCIO_TRASFERENTE."</a>";
            $linksocio_ricev = "<a class='text-green-light' style='text-decoration:none;color:lightblue;' href='sqldati_schedasocio.php?id=".$dati_c->IDSOCIO_RICEVENTE."'>".$dati_c->SOCIO_RICEVENTE."</a>";

            if ($dati_c->CTIPOMOV == 'TR') {$tipo = 'TR - Trasferimento (old)'; }
            elseif ($dati_c->CTIPOMOV == 'CO') {$tipo = 'CO - Compravendita'; }
            elseif ($dati_c->CTIPOMOV == 'SU') {$tipo = 'SU - Successione'; }
            elseif ($dati_c->CTIPOMOV == 'DO') {$tipo = 'DO - Donazione'; }
            elseif ($dati_c->CTIPOMOV == 'FU') {$tipo = 'FU - Fusione'; }
            else $tipo = '';

                echo "<tr>
                        <td><small>".$dati_c->NAG_TRASFERENTE."</td>
                        <td><small>".$dati_c->IDSOCIO_TRASFERENTE."</td>
                        <td><small>".$linksocio_trasf."</td>
                        <td><small>".$dati_c->NAG_RICEVENTE."</td>
                        <td><small>".$dati_c->IDSOCIO_RICEVENTE."</td>
                        <td><small>".$linksocio_ricev."</td>
                        <td><small>".$dati_c->DATA_MOVIMENTO."</td>
                        <td><small>".$tipo."</td>
                        <td align='right'><small>".number_format($dati_c->AZIONI,0,',','.')."</td>
                        <td align='right'><small>".number_format($dati_c->IMPORTO,2,',','.')."</td>
                      </tr>
                    ";

			}
			echo '</table>';
		?>

  </div>
  <div class="tab-pane fade" id="esclusioni">

  		<?php
  		// 
  		// ESCLUSIONI
  		//
			$select_e = "	SELECT e.Data_Richiesta, e.CDA, e.Numero_Azioni, e.Valore_Nominale,
							e.Escluso_art_6, e.Escluso_art_14, e.Escluso_x_Passaggio_a_Sofferenze,
							e.Data_InizioDecadenza, e.Data_Liquidazione, e.Data_Pagamento,
							e.MovimentoSicra, e.Note_Motivazioni, e.Pagamento, m.XMOTIVO
							FROM tab_xls_esclusioni e
							left join sds_soci s on e.IDSOCIO = s.IDSOCIO
							left join sds_soci_movinout m on m.idsocio = s.idsocio 
							WHERE e.IDSOCIO = ".$_GET['id']."
							GROUP BY e.Data_Richiesta
							-- GROUP BY e.nag
							ORDER BY STR_TO_DATE(e.Data_Richiesta, '%d/%m/%Y') 
						";

			//logquery ($select);  
			//echo $select_c;

			$querydati_e = mysqli_query($connection, $select_e);

			echo "<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
						<td align='center' rowspan='2'>Data Richiesta</td>
						<td align='center' rowspan='2'>Data CDA</td>
						<td align='center' rowspan='2'>Nr.<br>Azioni</td>
						<td align='center' rowspan='2'>Valore<br>Euro</td>
						<td align='center' colspan='3'>Tipo Esclusione</td>
						<td align='center' rowspan='2'>Data<br>Inizio Decad.</td>
						<td align='center' rowspan='2'>Data<br>Liquidazione</td>
						<td align='center' rowspan='2'>Data<br>Pagamento</td>
						<td align='center' rowspan='2'>MovSicra</td>
						<td align='center' rowspan='2'>Note</td>
						<td align='center' rowspan='2'>Info<br>Pagamento</td>
					</tr>
					<tr style='background-color:#6F6F6F;'>
						<td align='center'>Art.6</td>
						<td align='center'>Art.14</td>
						<td align='center'>Soffer</td>
					</tr>
						";

			while($dati_e=mysqli_fetch_array($querydati_e)){ 

			/*
			if 	($dati_e['Accoglimento_recesso'] == 'N') 
				{ $descrecesso = 'title ="Il recesso viene respinto in quanto non sussistono i requisiti da Statuto (il soggetto è ancora residente o ha attività o opera in zona di competenza)" style="background-color:brown;" ';}	
			else
				{ $descrecesso = ''; }
			*/

            if ( ($dati_e['Data_InizioDecadenza'] != '') 
            		AND ($dati_e['Data_Liquidazione'] == '') 
            		AND ($dati_e['Data_Pagamento'] == '') 
            	)
                {$tdstyle = "title ='QUOTE ANCORA A CAPITALE' style='background-color:#FAD6B4;color:black;' ";}

            elseif ( ($dati_e['Data_InizioDecadenza'] != '') 
            			AND ($dati_e['Data_Liquidazione'] != '') 
            			AND ($dati_e['Data_Pagamento'] == '') 
            		)
                {$tdstyle = "title ='QUOTE DA LIQUIDARE' style='background-color:#FAF0C6;color:black;' ";}

            elseif ( ($dati_e['Data_InizioDecadenza'] != '') 
            			AND ($dati_e['Data_Liquidazione'] != '') 
            			AND ($dati_e['Data_Pagamento'] != '') 
            		)
                {$tdstyle = "title ='QUOTE PAGATE' 
                	style='background-color:#86F586;color:black;' ";}

            elseif ( ($dati_e['Data_InizioDecadenza'] == '') 
            			AND ($dati_e['Data_Liquidazione'] == '') 
            			AND ($dati_e['Data_Pagamento'] == '') 
            		)
                {$tdstyle = "title ='RESPINTA' 
                	style='background-color:#222222;color:black;' ";}
                
			echo "	<tr>
						<td align='center'>".$dati_e['Data_Richiesta']."</td>
						<td align='center'>".$dati_e['CDA']."</td>
						<td align='center'>".$dati_e['Numero_Azioni']."</td>
						<td align='center'>".$dati_e['Valore_Nominale']."</td>
						<td align='center'>".$dati_e['Escluso_art_6']."</td>
						<td align='center'>".$dati_e['Escluso_art_14']."</td>
						<td align='center'>".$dati_e['Escluso_x_Passaggio_a_Sofferenze']."</td>
						<td align='center' ".$tdstyle.">".$dati_e['Data_InizioDecadenza']."</td>
						<td align='center' ".$tdstyle.">".$dati_e['Data_Liquidazione']."</td>
						<td align='center' ".$tdstyle.">".$dati_e['Data_Pagamento']."</td>
						<td align='center'>".$dati_e['MovimentoSicra']."</td>
						<td align='left'>".$dati_e['Note_Motivazioni']."</td>
						<td align='left'>".$dati_e['Pagamento']."<br>".$dati_e['XMOTIVO']."</td>
					</tr>";

			}
			echo '</table>';
		?>


  </div>
  <div class="tab-pane fade" id="decessi">

  		<?php
  		// DECESSI EREDI
  		//
			$select_d = "	SELECT 'SIB' as Fonte, e.Data_Richiesta, e.CDA, e.Numero_Azioni, e.Valore_Nominale,
							e.Data_Decesso, e.Anno_Liquidazione, e.Intestazione_a_eredi, e.Data, e.Note_AO08,
							e.Liquidazione_a_eredi, '' as CTIPOMOV, '' AS DATA_MOVIMENTO, '' as DATA_DELIBERA, '' as DATA_PAGAMENTO, 
							e.Note_Motivazioni, e.Pagamento, '' as XMOTIVO, '' as SOCIO_SUB, '' as SOCIO_EREDE
							FROM tab_xls_decessi_eredi_storico e
							WHERE e.NAG = ".$cag."
							GROUP BY e.nag

							UNION

							SELECT 'SICRA' as Fonte, '' as Data_Richiesta, '' as CDA, '' as Numero_Azioni, '' as Valore_Nominale,
							'' as Data_Decesso, '' as Anno_Liquidazione, '' as Intestazione_a_eredi, '' as Data, ''  as Note_AO08,
							'' as Liquidazione_a_eredi, m.CTIPOMOV, m.DATA_MOVIMENTO, m.DATA_DELIBERA, m.DATA_PAGAMENTO, 
							'' as Note_Motivazioni, '' as Pagamento, m.XMOTIVO, 
							x.IDSOCIO as SOCIO_SUB,
							concat(x.INTESTAZIONE_A, ' ', x.INTESTAZIONE_B) as SOCIO_EREDE

							FROM sds_soci_movinout m
							left join sds_soci s on m.idsocio = s.idsocio 
							left join sds_soci x on s.idsocio = x.idsocio_sub
							left join tab_xls_decessi_eredi_storico e on e.nag = s.NAG
							WHERE s.NAG = ".$cag."
							AND m.CTIPOMOV in ('MO','RL','RS')
							GROUP BY s.nag, m.CTIPOMOV
							
							ORDER BY STR_TO_DATE(Data_Richiesta, '%d/%m/%Y') 
						";

			//logquery ($select);  
			//echo $select_d;

			$querydati_d = mysqli_query($connection, $select_d);

			echo "<br>
				<table align='center' id='lista' border='1' width='100%'>
					<tr style='background-color:#6F6F6F;'>
						<td align='center'>Fonte</td>
						<td align='center'>Data Richiesta<br>Movimento</td>
						<td align='center'>Data CDA</td>
						<td align='center'>Nr.<br>Azioni</td>
						<td align='center'>Valore<br>Euro</td>
						<td align='center'>Data Decesso</td>
						<td align='center'>Intestaz<br>Eredi</td>
						<td align='center'>Liquidaz<br>Eredi</td>
						<td align='left'>Note</td>
						<td align='left'>Operazione</td>
						<td align='center'>Anno Liq. o<br>Data Pagam</td>
					</tr>";

			while($dati_d=mysqli_fetch_array($querydati_d)){ 

			if ($dati_d['CTIPOMOV'] == 'RS') {$tipomov = ' Subentro Eredi';}
			elseif ($dati_d['CTIPOMOV'] == 'RL') {$tipomov = ' Rimborso Eredi';}
			elseif ($dati_d['CTIPOMOV'] == 'MO') {$tipomov = ' Morte';}
			elseif ($dati_d['CTIPOMOV'] == 'ID') {$tipomov = ' Inizio Decadenza';}
			else {$tipomov = '';}

			if ($dati_d['Intestazione_a_eredi'] == 0) {$linksub = '';}
			if ($dati_d['SOCIO_SUB'] != 0) {$linksub = "<a title='".$dati_d['SOCIO_EREDE']."' style='text-decoration:none;color:lightblue;' href='sqldati_schedasocio.php?id=".$dati_d['SOCIO_SUB']."'>".$dati_d['SOCIO_SUB']."</a>";}

			echo "	<tr>
						<td align='center'>".$dati_d['Fonte']."</td>
						<td align='center'>".$dati_d['Data_Richiesta'].$dati_d['DATA_MOVIMENTO']."</td>
						<td align='center'>".$dati_d['CDA'].$dati_d['DATA_DELIBERA']."</td>
						<td align='center'>".$dati_d['Numero_Azioni']."</td>
						<td align='center'>".$dati_d['Valore_Nominale']."</td>
						<td align='center'>".$dati_d['Data_Decesso']."</td>
						<td align='center'>".$linksub."</td>
						<td align='center'>".$dati_d['Liquidazione_a_eredi']."</td>
						<td align='left'>
						<i class='fas fa-search-dollar fa-1x text-gray-300 col-auto' alt='".$dati_d['Pagamento']."' title='".$dati_d['Pagamento'].$dati_d['XMOTIVO']."'></i>
						".$dati_d['Note_Motivazioni']."</td>
						<td align='left'>".$dati_d['Data']." ".$dati_d['Note_AO08'].$dati_d['CTIPOMOV'].$tipomov."</td>
						<td align='center'>".$dati_d['Anno_Liquidazione'].$dati_d['DATA_PAGAMENTO']."</td>
					</tr>";

			}
			echo '</table>';
			//}
		?>

  </div>

<!-- ULTIMO DIV -->  
</div>		

</fieldset>

<!-- <button type="button" onclick="window.print()">Stampa</button> -->
<br><br><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
