<?php
//////////////////////////////////////////////////////////////////
// CREA TABELLA SDS_SOCI_DATI_CONSOLIDATI
// Viene richiamato nello scrito CREA_SDS_SOCI.PHP
// Author: Alessio Fedi - 03.11.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'CREA_SDS_SOCI_DATI_CONSOLIDATI';
$TITOLO = 'Raccolta Dati Consolidati';

include("config/_config.php");

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Connessione a MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

$dataout = date("d/m/Y", strtotime("-1 day"));

// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------

// -------------------------------------
// MAIL - Parametri invio Mail
// -------------------------------------
/*
      //No email sabato o domenica
      if(date('w')=="0" || date('w')=="6") return;
      
      //Parm email
      // ini_set('SMTP', 'smtp.crtnet'); 
      ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
      ini_set('smtp_port', 25); 
      ini_set('sendmail_from','soci@chiantibanca.it');
      
      $adesso = date('d/m');
      $orario1 = date("H:i:s");
      
      //Parm others
      //$mail_cc = $filiale_mail ; 
      //$mail_cc = "soci@chiantibanca.it"; 
      $nome_mittente = 'Ufficio Soci';
      $mail_dest     = 'soci@chiantibanca.it' ; 
      $mail_mittente = 'soci@chiantibanca.it' ; 
      $mail_oggetto  = "Situazione aggiornamento Tabelle SADAS DATI CONSOLIDATI > MySQL del ".$adesso;
      $mail_corpo    = '<html><body style="font-family:verdana;font-size:8.5pt;"> 
                          ';  


      $mail_corpo   .= '<br> Orario inizio : '.$orario1.'<br>';     
*/

//////////////////////////////////////////////////////////////////////////
// 1. QUERY: Creo la riga per l'inserimento dei dati e prendo il codice ID
//////////////////////////////////////////////////////////////////////////

$insert_datainserimento = mysqli_query($connection,
				"INSERT INTO SDS_SOCI_DATI_CONSOLIDATI (data_inserimento) VALUES (NOW())");

$result_LastID = mysqli_query($connection,"select max(id_soci_dati_consolidati) as LAST_ID from SDS_SOCI_DATI_CONSOLIDATI");
      $dati_LastID = mysqli_fetch_array($result_LastID);
      $LAST_ID = $dati_LastID['LAST_ID'];

//////////////////////////////////////////////////////////////////////////
//
// 2. ANNO, MESE, GIORNO: Carico i dati relativi all'anno
// mese e giorno di rilevazione
//
//////////////////////////////////////////////////////////////////////////

$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.DATA_RIF = NOW()
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

//////////////////////////////////////////////////////////////////////////
//
// 3. NAG TOTALI: Calcolo i NAG attivi totali
//
//////////////////////////////////////////////////////////////////////////

$select_NAG_TOT = "	SELECT 
						COUNT(anag.NAG) AS NAG_TOTALI
					FROM
						ANAG_NAG AS anag
					WHERE
						anag.STATO_NAG = '1'";

$result_NAG_TOT = odbc_exec($connect, $select_NAG_TOT);
$DATI_NAG_TOT = odbc_fetch_object($result_NAG_TOT);

// UPDATE: Aggiorno il campo del numero dei NAG totali
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.BA_NAG_TOTALI = '".$DATI_NAG_TOT->NAG_TOTALI."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);
//////////////////////////////////////////////////////////////////////////
//
// 4. SOCI TOTALI: Conto i SOCI attivi totali
//
//////////////////////////////////////////////////////////////////////////

$select_soci_TOT = "	SELECT
							COUNT(anagsoci.IDSOCIO) AS SOCI_TOTALI 
						FROM 
							SOCI_ANAGRAFICA AS anagsoci
						WHERE
							anagsoci.DATA_USCITA = '00/00/0000' ";

$result_soci_TOT = odbc_exec($connect, $select_soci_TOT);
$DATI_SOCI_TOT = odbc_fetch_object($result_soci_TOT);						

// UPDATE: Aggiorno il campo del numero dei SOCI totali e quello dei NON SOCI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_NAG_TOTALI = '".$DATI_SOCI_TOT->SOCI_TOTALI."',
						SDC.NS_NAG_TOTALI = '".($DATI_NAG_TOT->NAG_TOTALI - $DATI_SOCI_TOT->SOCI_TOTALI)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);						

//////////////////////////////////////////////////////////////////////////
//
// 5. CC TOTALI: Conto i CC attivi
//
//////////////////////////////////////////////////////////////////////////

$select_CC_TOT = "	SELECT
						COUNT(cc.NUM_RAPP) AS CC_TOTALI
					FROM
						CC_CONTI_CORRENTI AS cc
					WHERE
						cc.STATO = '0'
					AND
						cc.COD_RAPP = 2 ";

$result_CC_TOT = odbc_exec($connect, $select_CC_TOT);
$DATI_CC_TOT = odbc_fetch_object($result_CC_TOT);	

// UPDATE: Aggiorno il campo del numero dei CC totali
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.BA_CC_TOTALI = '".$DATI_CC_TOT->CC_TOTALI."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);						

//////////////////////////////////////////////////////////////////////////
//
// 6. CC SOCI: Conto i CC dei SOCI
//
//////////////////////////////////////////////////////////////////////////

$select_CC_SOCI = "	SELECT
						COUNT(cc.NUM_RAPP) AS CC_SOCI
					FROM
						CC_CONTI_CORRENTI AS cc
					JOIN
						SOCI_ANAGRAFICA AS soci ON cc.NAG = soci.NAG
					WHERE
						cc.STATO = '0'
					AND
						cc.COD_RAPP = 2
					AND
						soci.DATA_USCITA = '00/00/0000'";

$result_CC_SOCI = odbc_exec($connect, $select_CC_SOCI);
$DATI_CC_SOCI = odbc_fetch_object($result_CC_SOCI);						

// UPDATE: Aggiorno il campo del numero dei CC dei SOCI e quello dei NON SOCI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_CC_TOTALI = '".$DATI_CC_SOCI->CC_SOCI."',
						SDC.NS_CC_TOTALI = '".($DATI_CC_TOT->CC_TOTALI - $DATI_CC_SOCI->CC_SOCI)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

//////////////////////////////////////////////////////////////////////////
//
// 7. SOCI PF: Seleziono i SOCI persone fisiche
//
////////////////////////////////////////////////////////////////////////

$select_PF_SOCI = "	SELECT
						COUNT(anagsoci.IDSOCIO) AS SOCI_PF
					FROM 
						SOCI_ANAGRAFICA AS anagsoci
					JOIN
						ANAG_NAG AS anag ON anag.NAG = anagsoci.NAG
					WHERE
						anagsoci.DATA_USCITA = '00/00/0000'
					AND
						anag.TIPO_NAG = 'PF'";

$result_PF_SOCI = odbc_exec($connect, $select_PF_SOCI);
$DATI_PF_SOCI = odbc_fetch_object($result_PF_SOCI);						

// UPDATE: Aggiorno il campo del numero dei SOCI PF e quelli PG
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_PF = '".$DATI_PF_SOCI->SOCI_PF."',
						SDC.SO_PG = '".($DATI_SOCI_TOT->SOCI_TOTALI - $DATI_PF_SOCI->SOCI_PF)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);						

//////////////////////////////////////////////////////////////////////////
//
// 8. SOCI PF: Seleziono i SOCI PER SESSO
//
////////////////////////////////////////////////////////////////////////

$select_PF_SOCI_M = "	SELECT
							COUNT(anagsoci.IDSOCIO) AS SOCI_UOMINI
						FROM 
							SOCI_ANAGRAFICA AS anagsoci
						JOIN
							ANAG_NAG AS anag ON anag.NAG = anagsoci.NAG
						JOIN
							ANAG_PERSONE_FISICHE AS anagpf ON anagpf.NAG = anagsoci.NAG
						WHERE
							anagsoci.DATA_USCITA = '00/00/0000'
						AND
							anag.TIPO_NAG = 'PF'
						AND
							anagpf.SESSO = 'M'";

$result_PF_SOCI_M = odbc_exec($connect, $select_PF_SOCI_M);
$DATI_PF_SOCI_M = odbc_fetch_object($result_PF_SOCI_M);	

// UPDATE: Aggiorno il campo del numero dei SOCI uomini e delle donne
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_PF_UOMINI = '".$DATI_PF_SOCI_M->SOCI_UOMINI."',
						SDC.SO_PF_DONNE = '".($DATI_PF_SOCI->SOCI_PF - $DATI_PF_SOCI_M->SOCI_UOMINI)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);						

//////////////////////////////////////////////////////////////////////////
//
// 9. FASCE ETA' SOCI
// Definisco le fasce di età da considerare e le inserisco
// in un Array con ($FASCIA => $ETA_DI_PARTENZA|$ETA_DI_FINE)
//
////////////////////////////////////////////////////////////////////////

$ARRAY_FASCE = array (	'1' => '18|30',
						'2' => '30|35',
						'3' => '35|40',
						'4' => '40|45',
						'5' => '45|50',
						'6' => '50|55',
						'7' => '55|60',
						'8' => '60|65',
						'9' => '65|70',
						'10' => '70|75',
						'11' => '75|80',
						'12' => '80|200');

// FOREACH: Per ogni fascia definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FASCE AS $FASCIA => $tmp_eta) {

	$ETA = explode('|', $tmp_eta);

	// I conti delle fasce "sballano" per le persone che sono nate nel giorno dell'analisi di N anni fa
	// perchè vengono conteggiati sia in una fascia che nell'altra.
	// Per risolvere questo problema, la data di verifica del "massimo" ($ANNI_MAX) viene anticipata di 1 giorno
	$ANNI_MIN = '-'.$ETA[0].' year';
	$ANNI_MAX = '-'.$ETA[1].' year 1 day';

	$DATA_INIZIO = date('Y-m-d', strtotime($ANNI_MIN));
	$DATA_FINE = date('Y-m-d', strtotime($ANNI_MAX));

	$select_fascia_eta = "	SELECT
								COUNT(anagsoci.IDSOCIO) AS SOCI_FASCIA
							FROM 
								SOCI_ANAGRAFICA AS anagsoci
							JOIN
								ANAG_NAG AS anag ON anag.NAG = anagsoci.NAG
							JOIN
								ANAG_PERSONE_FISICHE AS anagpf ON anagpf.NAG = anagsoci.NAG
							WHERE
								anagsoci.DATA_USCITA = '00/00/0000'
							AND
								anag.TIPO_NAG = 'PF'
							AND
								anagpf.DATA_NASCITA BETWEEN '".$DATA_FINE."' AND '".$DATA_INIZIO."'";

	$result_fascia_eta = odbc_exec($connect, $select_fascia_eta);
	$DATI_FASCIA = odbc_fetch_object($result_fascia_eta);	

	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_ETA_FASCIA".$FASCIA." = '".$DATI_FASCIA->SOCI_FASCIA."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	$esegui = mysqli_query($connection, $update_query);
}

//////////////////////////////////////////////////////////////////////////
//
// 10. TOTALE QUOTE SOCI
// Estraggo il totale delle quote attualmente vendute
//
////////////////////////////////////////////////////////////////////////

$select_TOT_QUOTE = "
                              SELECT sum(cert.NAZIONI) as TOT_QUOTE
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO <= '".$dataout."')
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$dataout."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$dataout."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ";
/*
$select_TOT_QUOTE = "	SELECT
							SUM(certif.NAZIONI) AS TOT_QUOTE
						FROM
							SOCI_ANAGRAFICA AS anagsoci
						JOIN
							SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
						WHERE
							anagsoci.DATA_USCITA = '00/00/0000'
						AND
							certif.DATA_VENDITA = '00/00/0000'
						AND
							certif.DATA_ANNULLAMENTO = '00/00/0000'";
*/
$result_TOT_QUOTE = odbc_exec($connect, $select_TOT_QUOTE);
$DATI_TOT_QUOTE = odbc_fetch_object($result_TOT_QUOTE);

// UPDATE: Aggiorno il campo del numero delle quote totali e del patrimonio SOCI della Banca
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.BA_TOT_QUOTE = '".$DATI_TOT_QUOTE->TOT_QUOTE."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);


//////////////////////////////////////////////////////////////////////////
//
// 10b. CONTABILITA
// Estraggo i saldi dei conti contabili Soci e li scrivo in tabelle a parte
//
////////////////////////////////////////////////////////////////////////

$select_COGE = "
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
	                            CG_SALDI.COD_RAPP IN ( 2881,2885,1770 ) 
	                        AND
	                            CG_SALDI.FILIALE = 990 
	                        AND
	                            CG_SALDI.NUM_RAPP = 100 
	                        ORDER BY
	                             SALDO ASC
                              ";

$result_COGE = odbc_exec($connect, $select_COGE);
//$DATI_COGE = odbc_fetch_object($result_COGE);
        while($dati_saldi_coge = odbc_fetch_object($result_COGE)) {

            if ($dati_saldi_coge->COD_RAPP == 2881) 
            	{$capitale_per_tab_valorefondo = $dati_saldi_coge->SALDO;}
            if ($dati_saldi_coge->COD_RAPP == 2885) 
            	{$sovrapprezzo_per_tab_valorefondo = $dati_saldi_coge->SALDO;}
            if ($dati_saldi_coge->COD_RAPP == 1770) 
            	{$fondo_per_tab_valorefondo = $dati_saldi_coge->SALDO;}

}

// UPDATE: Aggiorno i dati contabili
$update_query = "	UPDATE
						TAB_VALOREFONDO 
					SET
	                              aggiornamento = now(), 
	                              valore = '".$fondo_per_tab_valorefondo."',
	                              capitale = '".$capitale_per_tab_valorefondo."',
	                              sovrapprezzo = '".$sovrapprezzo_per_tab_valorefondo."'" ;
echo $update_query;
$esegui = mysqli_query($connection, $update_query);


// UPDATE2: Aggiorno il campo del numero delle quote totali e del patrimonio SOCI della Banca
$update_query2 = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.BA_PATRIMONIO = '".$capitale_per_tab_valorefondo."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui2 = mysqli_query($connection, $update_query2);

//////////////////////////////////////////////////////////////////////////
//
// 11. FASCE QUOTE SOCI
// Definisco le fasce di quote possedute e le inserisco 
// in un Array con ($FASCIA => $QUANTITA_MINIMA|$QUANTITA_MASSIMA)
//
////////////////////////////////////////////////////////////////////////

$ARRAY_FASCE = array (	'1' => '0|33',
						'2' => '33|50',
						'3' => '51|100',
						'4' => '101|200',
						'5' => '201|500',
						'6' => '501|1000',
						'7' => '1001|99999999');

// FOREACH: Per ogni fascia definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FASCE AS $FASCIA => $tmp_quote) {

	$QUOTE = explode('|', $tmp_quote);

	$select_fascia_quote = "	SELECT COUNT(*) AS QUOTE_FASCIA
								FROM (

									SELECT
										anagsoci.IDSOCIO
									FROM
										SOCI_ANAGRAFICA AS anagsoci
									JOIN
										SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
									WHERE
										anagsoci.DATA_USCITA = '00/00/0000'
									AND
										certif.DATA_VENDITA = '00/00/0000'
									AND
										certif.DATA_ANNULLAMENTO = '00/00/0000'
									AND 
										certif.NAZIONI BETWEEN ".$QUOTE[0]." AND ".$QUOTE[1]."
									GROUP BY
										anagsoci.IDSOCIO
									
								) AS QUERY_EXT";

	$result_fascia_quote = odbc_exec($connect, $select_fascia_quote);
	$DATI_FASCIA = odbc_fetch_object($result_fascia_quote);
	
	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_QUOTE_FASCIA".$FASCIA." = '".$DATI_FASCIA->QUOTE_FASCIA."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	$esegui = mysqli_query($connection, $update_query);
}

//////////////////////////////////////////////////////////////////////////
//
// 12. SOCI PER FILIALE
//
////////////////////////////////////////////////////////////////////////

// Definisco le filiali in un Array
$ARRAY_FILIALI = array ( 0
						,1
						,2
						,3
						,4
						,5
						,20
						,21
						,22
						,23
						,24
						,25
						,26
						,30
						,32
						,33
						,35
						,36
						,40
						,41
						,43
						,44
						,50
						,51
						,53
						,54
						,55
						,56
						,60
						,61
						,62
						,63
						,64
						,66
						,67
						,70
						,71
						,73
						,100);

// FOREACH: Per ogni filiale definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FILIALI AS $FILIALE) {

	$select_filiale_num = "	SELECT
								COUNT(anagsoci.IDSOCIO) AS SOCI_FILIALE
							FROM 
								SOCI_ANAGRAFICA AS anagsoci
							JOIN
								ANAG_NAG AS anag ON anag.NAG = anagsoci.NAG
							WHERE
								anagsoci.DATA_USCITA = '00/00/0000'
							AND
								anag.FILIALE_CAPOFILA = ".$FILIALE." ";

	$result_filiale_num = odbc_exec($connect, $select_filiale_num);
	$DATI_FILIALE_N = odbc_fetch_object($result_filiale_num);
	
	$select_filiale_quote = "	SELECT
									SUM(certif.NAZIONI) AS TOT_QUOTE_FILIALE
								FROM
									SOCI_ANAGRAFICA AS anagsoci
								JOIN
									SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
								JOIN
									ANAG_NAG AS anag ON anagsoci.NAG = anag.NAG
								WHERE
									anagsoci.DATA_USCITA = '00/00/0000'
								AND
									certif.DATA_VENDITA = '00/00/0000'
								AND
									certif.DATA_ANNULLAMENTO = '00/00/0000'
								AND
									anag.FILIALE_CAPOFILA = ".$FILIALE." ";

	$result_filiale_quote = odbc_exec($connect, $select_filiale_quote);
	$DATI_FILIALE_Q = odbc_fetch_object($result_filiale_quote);								

	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query =  "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_FILIALE_".$FILIALE." = '".$DATI_FILIALE_N->SOCI_FILIALE."',
							SDC.SO_QUOTE_FILIALE_".$FILIALE." = '".$DATI_FILIALE_Q->TOT_QUOTE_FILIALE."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";
	
	$esegui = mysqli_query($connection, $update_query);

}

//////////////////////////////////////////////////////////////////////////
//
// 13. QUOTE SOTTOSCRITTE PER ANNO
//
////////////////////////////////////////////////////////////////////////

// Definisco le date delle fasce da analizzare
$ARRAY_PERIODI = array (
			'2016' => '01/01/1900|31/12/2016',
			'2017' => '01/01/2017|31/12/2017',
			'2018' => '01/01/2018|31/12/2018',
			'2019' => '01/01/2019|31/12/2019',
			'2020' => '01/01/2020|31/12/2020',
			'2021' => '01/01/2021|31/12/2021',
			'2022' => '01/01/2022|31/12/2022',
			'2023' => '01/01/2023|31/12/2023',
			'2024' => '01/01/2024|31/12/2024',
			'2025' => '01/01/2025|31/12/2025',
			'2026' => '01/01/2026|31/12/2026'
			);

// FOREACH: Per ogni periodo definito nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_PERIODI AS $PERIODO => $tmp_date) {

	$DATE = explode('|', $tmp_date);

	$select_fascia_quote = "	SELECT
									SUM(certif.NAZIONI) AS QUOTE_PERIODO
								FROM
									SOCI_ANAGRAFICA AS anagsoci
								JOIN
									SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
								WHERE
									anagsoci.DATA_USCITA = '00/00/0000'
								AND
									certif.DATA_VENDITA = '00/00/0000'
								AND
									certif.DATA_ANNULLAMENTO = '00/00/0000'
								AND
									certif.DATA_ACQUISTO BETWEEN '".$DATE[0]."' AND '".$DATE[1]."'";

	$result_fascia_quote = odbc_exec($connect, $select_fascia_quote);
	$DATI_PERIODO = odbc_fetch_object($result_fascia_quote);	
	
	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = "	UPDATE
					SDS_SOCI_DATI_CONSOLIDATI AS SDC
				SET
					SDC.SO_QUOTE_PER_ANNO_FASCIA_".$PERIODO." = '".$DATI_PERIODO->QUOTE_PERIODO."'
				WHERE
					SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	$esegui = mysqli_query($connection, $update_query);
}

////////////////////////////////////////////////////////////////////////
//
// 14. QUOTE SOTTOSCRITTE PER TRIMESTRE ANNO CORRENTE
//
////////////////////////////////////////////////////////////////////////

// Anno Corrente
$ANNO_CORR = date('Y');

// Definisco le date delle fasce da analizzare

/*$ARRAY_PERIODI = array (	'1' => $ANNO_CORR.'-01-01|'.$ANNO_CORR.'-03-31',
							'2' => $ANNO_CORR.'-04-01|'.$ANNO_CORR.'-06-30',
							'3' => $ANNO_CORR.'-07-01|'.$ANNO_CORR.'-09-30',
							'4' => $ANNO_CORR.'-10-01|'.$ANNO_CORR.'-12-31');
*/
$ARRAY_PERIODI = array (	'1' => '01/01/'.$ANNO_CORR.'|'.'31/03/'.$ANNO_CORR,
							'2' => '01/04/'.$ANNO_CORR.'|'.'30/06/'.$ANNO_CORR,
							'3' => '01/07/'.$ANNO_CORR.'|'.'30/09/'.$ANNO_CORR,
							'4' => '01/10/'.$ANNO_CORR.'|'.'31/12/'.$ANNO_CORR);

// FOREACH: Per ogni periodo definito nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_PERIODI AS $PERIODO => $tmp_date) {

	$DATE = explode('|', $tmp_date);

	$select_fascia_quote = "	SELECT
									SUM(certif.NAZIONI) AS QUOTE_PERIODO
								FROM
									SOCI_ANAGRAFICA AS anagsoci
								JOIN
									SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
								WHERE
									anagsoci.DATA_USCITA = '00/00/0000'
								AND
									certif.DATA_VENDITA = '00/00/0000'
								AND
									certif.DATA_ANNULLAMENTO = '00/00/0000'
								AND
									certif.DATA_ACQUISTO BETWEEN '".$DATE[0]."' AND '".$DATE[1]."'";

	$result_fascia_quote = odbc_exec($connect, $select_fascia_quote);
	$DATI_PERIODO = odbc_fetch_object($result_fascia_quote);
	
	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_QUOTE_ULTIMO_ANNO_TRIM".$PERIODO." = '".$DATI_PERIODO->QUOTE_PERIODO."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	$esegui = mysqli_query($connection, $update_query);

}
/*
////////////////////////////////////////////////////////////////////////
//
// 15. CC SOCI CON CARATTERISTICA SOCIO/GIOVANE SOCIO
// classe 248001 (SOCI IMPRESA)
// classe 255000 (SOCI)
// classe 280000 (GIOVANI SOCI)
//
////////////////////////////////////////////////////////////////////////

$select_CC_SOCI_S = "	SELECT
							COUNT(cc.NUM_RAPP) AS CC_SOCI_S
						FROM
							CC_CONTI_CORRENTI AS cc
						JOIN
							SOCI_ANAGRAFICA AS soci ON cc.NAG = soci.NAG
						WHERE
							cc.STATO = '0'
						AND
							cc.COD_RAPP = '2'
						AND
							soci.DATA_USCITA = '00/00/0000'
						AND
							cc.COD_CLASSE IN ('248001','255000','280000')";

$result_CC_SOCI_S = odbc_exec($connect, $select_CC_SOCI_S);
$DATI_CC_SOCI_S = $result_CC_SOCI_S->fetch_object();						

// UPDATE: Aggiorno il campo del numero dei CC dei SOCI e quello dei NON SOCI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_CC_CARATT_SOCIO = '".$DATI_CC_SOCI_S->CC_SOCI_S."',
						SDC.SO_CC_NO_CARATT_SOCIO = '".($DATI_CC_SOCI->CC_SOCI - $DATI_CC_SOCI_S->CC_SOCI_S)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";


	$esegui = mysqli_query($connection, $update_query);


////////////////////////////////////////////////////////////////////////
//
// 15a. CC SOCI CON CARATTERISTICA SOCIO/GIOVANE SOCIO
// DIVISI PER PF E PG
// classe 248001 (SOCI IMPRESA)
// classe 255000 (SOCI)
// classe 280000 (GIOVANI SOCI)
//
////////////////////////////////////////////////////////////////////////

$select_CC_SOCI_S_PF = "	SELECT
								COUNT(cc.NUM_RAPP) AS CC_SOCI_S_PF
							FROM
								CC_CONTI_CORRENTI AS cc
							JOIN
								SOCI_ANAGRAFICA AS soci ON cc.NAG = soci.NAG
							JOIN
								ANAG_NAG AS anag ON cc.NAG = anag.NAG
							WHERE
								cc.STATO = '0'
							AND
								cc.COD_RAPP = '2'
							AND
								soci.DATA_USCITA = '00/00/0000'
							AND
								anag.TIPO_NAG = 'PF'
							AND
								cc.COD_CLASSE IN ('248001','255000','280000')";

$result_CC_SOCI_S_PF = odbc_exec($connect, $select_CC_SOCI_S_PF);
$DATI_CC_SOCI_S_PF = $result_CC_SOCI_S_PF->fetch_object();						

// UPDATE: Aggiorno il campo del numero dei CC dei SOCI e quello dei NON SOCI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_CC_CARATT_SOCIO_PF = '".$DATI_CC_SOCI_S_PF->CC_SOCI_S_PF."',
						SDC.SO_CC_CARATT_SOCIO_PG = '".($DATI_CC_SOCI_S->CC_SOCI_S - $DATI_CC_SOCI_S_PF->CC_SOCI_S_PF)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 15b. CC SOCI CHE NON HANNO CARATTERISTICA SOCIO/GIOVANE SOCIO
// DIVISI PER PF E PG
//
////////////////////////////////////////////////////////////////////////

$select_CC_SOCI_NO_S_PF = "	SELECT
								COUNT(cc.NUM_RAPP) AS CC_SOCI_S_PF
							FROM
								CC_CONTI_CORRENTI AS cc
							JOIN
								SOCI_ANAGRAFICA AS soci ON cc.NAG = soci.NAG
							JOIN
								ANAG_NAG AS anag ON cc.NAG = anag.NAG
							WHERE
								cc.STATO = '0'
							AND
								cc.COD_RAPP = '2'
							AND
								soci.DATA_USCITA = '00/00/0000'
							AND
								anag.TIPO_NAG = 'PF'
							AND
								cc.COD_CLASSE NOT IN ('248001','255000','280000')";

$result_CC_SOCI_NO_S_PF = odbc_exec($connect, $select_CC_SOCI_NO_S_PF);
$DATI_CC_SOCI_NO_S_PF = $result_CC_SOCI_NO_S_PF->fetch_object();						

// UPDATE: Aggiorno il campo del numero dei CC dei SOCI e quello dei NON SOCI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_CC_NO_CARATT_SOCIO_PF = '".$DATI_CC_SOCI_NO_S_PF->CC_SOCI_S_PF."',
						SDC.SO_CC_NO_CARATT_SOCIO_PG = '".(($DATI_CC_SOCI->CC_SOCI - $DATI_CC_SOCI_S->CC_SOCI_S) - $DATI_CC_SOCI_NO_S_PF->CC_SOCI_S_PF)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);
*/
////////////////////////////////////////////////////////////////////////
//
// 16. NUMERO GIOVANI SOCI
// TUTTI I SOCI CON ETA' INFERIORE A 30 ANNI
//
////////////////////////////////////////////////////////////////////////

$ANNI = '-30 year';
$DATA_FINE = date('d/m/Y', strtotime($ANNI));

$select_giovanisoci_TOT = "			SELECT COUNT(*) AS GIOVANI_SOCI_TOT
									FROM (

										SELECT
											anagsoci.IDSOCIO
										FROM
											SOCI_ANAGRAFICA AS anagsoci
										JOIN
											SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
										WHERE
											anagsoci.DATA_USCITA = '00/00/0000'
										AND
											certif.DATA_VENDITA = '00/00/0000'
										AND
											certif.DATA_ANNULLAMENTO = '00/00/0000'
										AND
											anagsoci.DATA_NASCITA >= '".$DATA_FINE."'
										GROUP BY
											anagsoci.IDSOCIO

									) AS QUERY_EXT";

$result_giovanisoci_TOT = odbc_exec($connect, $select_giovanisoci_TOT);
$DATI_GIOVANISOCI = odbc_fetch_object($result_giovanisoci_TOT);

// UPDATE: Aggiorno il campo del numero dei GIOVANI SOCI e quello dei SOCI ORDINARI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_NAG_PF_GIOV = '".$DATI_GIOVANISOCI->GIOVANI_SOCI_TOT."',
						SDC.SO_NAG_PF_ORD = '".($DATI_PF_SOCI->SOCI_PF - $DATI_GIOVANISOCI->GIOVANI_SOCI_TOT)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 16a. NUMERO GIOVANI SOCI
// TUTTI I SOCI CON ETA' INFERIORE A 30 ANNI
// CHE HANNO 33 QUOTE O PIU'
//
////////////////////////////////////////////////////////////////////////

$ANNI = '-30 year';
$DATA_FINE = date('d/m/Y', strtotime($ANNI));

$select_giovanisoci_33_az = "		SELECT COUNT(*) AS GIOVANI_SOCI_33_AZ

									FROM (

										SELECT
											anagsoci.IDSOCIO
										FROM
											SOCI_ANAGRAFICA AS anagsoci
										JOIN
											SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
										WHERE
											anagsoci.DATA_USCITA = '00/00/0000'
										AND
											certif.DATA_VENDITA = '00/00/0000'
										AND
											certif.DATA_ANNULLAMENTO = '00/00/0000'
										AND
											anagsoci.DATA_NASCITA >= '".$DATA_FINE."'
										AND 
											certif.NAZIONI = 33
										GROUP BY
											anagsoci.IDSOCIO

									) AS QUERY_EXT";

$result_giovanisoci_33_az = odbc_exec($connect, $select_giovanisoci_33_az);
$DATI_GIOVANISOCI_33_AZ =  odbc_fetch_object($result_giovanisoci_33_az);

// UPDATE: Aggiorno il campo del numero dei GIOVANI SOCI e quello dei SOCI ORDINARI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_NAG_PF_GIOV_33_QUOTE = '".$DATI_GIOVANISOCI_33_AZ->GIOVANI_SOCI_33_AZ."',
						SDC.SO_NAG_PF_GIOV_PIU_33_QUOTE = '".($DATI_GIOVANISOCI->GIOVANI_SOCI_TOT - $DATI_GIOVANISOCI_33_AZ->GIOVANI_SOCI_33_AZ)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 16b. NUMERO SOCI ORDINARI
// TUTTI I SOCI CON ETA' SUPERIORE A 30 ANNI
// CHE HANNO MENO DI 33 QUOTE
//
////////////////////////////////////////////////////////////////////////

$ANNI = '-30 year';
$DATA_FINE = date('d/m/Y', strtotime($ANNI));

$select_sociord_meno_33_az = "		SELECT COUNT(*) AS SOCIORD_MENO_33_AZ

									FROM (

										SELECT
											anagsoci.IDSOCIO
										FROM
											SOCI_ANAGRAFICA AS anagsoci
										JOIN
											SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
										WHERE
											anagsoci.DATA_USCITA = '00/00/0000'
										AND
											certif.DATA_VENDITA = '00/00/0000'
										AND
											certif.DATA_ANNULLAMENTO = '00/00/0000'
										AND
											anagsoci.DATA_NASCITA < '".$DATA_FINE."'
										AND 
											certif.NAZIONI < 33
										GROUP BY
											anagsoci.IDSOCIO

									) AS QUERY_EXT";

$result_sociord_meno_33_az = odbc_exec($connect, $select_sociord_meno_33_az);
$DATI_SOCIORD_MENO_33_AZ = odbc_fetch_object($result_sociord_meno_33_az);

// UPDATE: Aggiorno il campo del numero dei GIOVANI SOCI e quello dei SOCI ORDINARI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_NAG_PF_ORD_MENO_33_QUOTE = '".$DATI_SOCIORD_MENO_33_AZ->SOCIORD_MENO_33_AZ."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 16c. NUMERO SOCI ORDINARI
// TUTTI I SOCI CON ETA' SUPERIORE A 30 ANNI
// CHE HANNO 33 QUOTE
//
////////////////////////////////////////////////////////////////////////

$ANNI = '-30 year';
$DATA_FINE = date('d/m/Y', strtotime($ANNI));

$select_sociord_33_az = "			SELECT COUNT(*) AS SOCIORD_33_AZ

									FROM (

										SELECT
											anagsoci.IDSOCIO
										FROM
											SOCI_ANAGRAFICA AS anagsoci
										JOIN
											SOCI_CERTIFICATI AS certif ON certif.IDSOCIO = anagsoci.IDSOCIO
										WHERE
											anagsoci.DATA_USCITA = '00/00/0000'
										AND
											certif.DATA_VENDITA = '00/00/0000'
										AND
											certif.DATA_ANNULLAMENTO = '00/00/0000'
										AND
											anagsoci.DATA_NASCITA < '".$DATA_FINE."'
										AND 
											certif.NAZIONI = 33
										GROUP BY
											anagsoci.IDSOCIO

									) AS QUERY_EXT";

$result_sociord_33_az = odbc_exec($connect, $select_sociord_33_az);
$DATI_SOCIORD_33_AZ = odbc_fetch_object($result_sociord_33_az);

// UPDATE: Aggiorno il campo del numero dei GIOVANI SOCI e quello dei SOCI ORDINARI
$update_query =  "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_NAG_PF_ORD_33_QUOTE = '".$DATI_SOCIORD_33_AZ->SOCIORD_33_AZ."',
						SDC.SO_NAG_PF_ORD_PIU_33_QUOTE = '".(($DATI_PF_SOCI->SOCI_PF - $DATI_GIOVANISOCI->GIOVANI_SOCI_TOT) - ($DATI_SOCIORD_MENO_33_AZ->SOCIORD_MENO_33_AZ + $DATI_SOCIORD_33_AZ->SOCIORD_33_AZ))."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 17. RAPPORTI DI CC DEI SOCI AFFIDATI
//
////////////////////////////////////////////////////////////////////////

$select_CC_SOCI_aff = "		SELECT
								COUNT(cc.NUM_RAPP) AS CC_SOCI_AFF
							FROM
								CC_CONTI_CORRENTI AS cc
							JOIN
								SOCI_ANAGRAFICA AS soci ON cc.NAG = soci.NAG
							JOIN
								FIDI AS fidi ON cc.NUM_RAPP = fidi.NUM_RAPP_CAS AND cc.COD_RAPP = fidi.COD_RAPP_CAS AND cc.FILIALE = fidi.FILIALE_CAS
							WHERE
								cc.STATO = '0'
							AND
								cc.COD_RAPP = 2
							AND
								soci.DATA_USCITA = '00/00/0000'
							AND
								fidi.DATA_SCAD = '00/00/0000'
							AND
								fidi.CATEGORIA_CR = 3 ";

$result_CC_SOCI_aff = odbc_exec($connect, $select_CC_SOCI_aff);
$DATI_CC_SOCI_AFF = odbc_fetch_object($result_CC_SOCI_aff);

// UPDATE: Aggiorno il campo del numero dei CC dei SOCI affidati e quello dei NON affidati
$update_query = "	UPDATE
				SDS_SOCI_DATI_CONSOLIDATI AS SDC
			SET
				SDC.SO_CC_AFFIDATI = '".$DATI_CC_SOCI_AFF->CC_SOCI_AFF."',
				SDC.SO_CC_NO_AFFIDATI = '".($DATI_CC_SOCI->CC_SOCI - $DATI_CC_SOCI_AFF->CC_SOCI_AFF)."'
			WHERE
				SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 17a. RAPPORTI DI CC AFFIDATI DEI SOCI 
// DIVISI TRA PERSONE FISICHE E PERSONE GIURIDICHE
//
////////////////////////////////////////////////////////////////////////

$select_CC_SOCI_AFF_PF = "		SELECT
									COUNT(cc.NUM_RAPP) AS CC_SOCI_AFF_PF
								FROM
									CC_CONTI_CORRENTI AS cc
								JOIN
									SOCI_ANAGRAFICA AS soci ON cc.NAG = soci.NAG
								JOIN
									ANAG_NAG AS anag ON cc.NAG = anag.NAG
								JOIN
									FIDI AS fidi ON cc.NUM_RAPP = fidi.NUM_RAPP_CAS AND cc.COD_RAPP = fidi.COD_RAPP_CAS AND cc.FILIALE = fidi.FILIALE_CAS
								WHERE
									cc.STATO = '0'
								AND
									cc.COD_RAPP = 2
								AND
									soci.DATA_USCITA = '00/00/0000'
								AND
									fidi.DATA_SCAD = '00/00/0000'
								AND
									anag.TIPO_NAG = 'PF'
								AND
									fidi.CATEGORIA_CR = 3 ";

$result_CC_SOCI_AFF_PF = odbc_exec($connect, $select_CC_SOCI_AFF_PF);
$DATI_CC_SOCI_AFF_PF = odbc_fetch_object($result_CC_SOCI_AFF_PF);

// UPDATE: Aggiorno il campo del numero dei CC dei SOCI affidati e quello dei NON affidati
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_CC_AFFIDATI_PF = '".$DATI_CC_SOCI_AFF_PF->CC_SOCI_AFF_PF."',
						SDC.SO_CC_AFFIDATI_PG = '".($DATI_CC_SOCI_AFF->CC_SOCI_AFF - $DATI_CC_SOCI_AFF_PF->CC_SOCI_AFF_PF)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

// ---------------------------------------------------
// RECUPERO PERIODO SAR PIU' RECENTE
// ---------------------------------------------------

$select_SAR_ANNOMESERIF = "	SELECT
								max(sar.ANNO_MESE_RIF) as ANNO_MESE_RIF
							FROM
								SAR_DATI_POSIZIONE AS sar
							";

$result_SAR_ANNOMESERIF = odbc_exec($connect, $select_SAR_ANNOMESERIF);
$DATI_SAR_ANNOMESERIF = odbc_fetch_object($result_SAR_ANNOMESERIF);

$ANNOMESERIF = $DATI_SAR_ANNOMESERIF->ANNO_MESE_RIF;

////////////////////////////////////////////////////////////////////////
//
// 18. ACCORDATO/UTILIZZATO/DEPOSITI TOTALI
//
////////////////////////////////////////////////////////////////////////

$select_SAR_TOT = "		SELECT
							SUM(QUERY1.TMP_TOTALE_ACCORDATO) AS TOTALE_ACCORDATO,
							SUM(QUERY1.TMP_TOTALE_UTILIZZATO) AS TOTALE_UTILIZZATO,
							SUM(QUERY1.TMP_TOTALE_DEPOSITI) AS TOTALE_DEPOSITI

						FROM

							(SELECT
								anag.NAG,
								Sum(sar.TOTALE_ACCORDATO) AS TMP_TOTALE_ACCORDATO,
								Sum(sar.TOTALE_UTILIZZATO) AS TMP_TOTALE_UTILIZZATO,
								Sum(sar.TOTALE_DEPOSITI) AS TMP_TOTALE_DEPOSITI,
								sar.ANOM_INT_100,
								sar.ANOM_INT_101
							FROM
								SAR_DATI_POSIZIONE AS sar
							JOIN
								ANAG_NAG AS anag ON anag.NAG = sar.NAG
							WHERE
								sar.ANOM_INT_100 = ''
							AND
								anag.STATO_NAG = '1'
							AND 
								sar.ANNO_MESE_RIF = '".$ANNOMESERIF."'
							GROUP BY
								anag.NAG, sar.ANOM_INT_100, sar.ANOM_INT_101) AS QUERY1";

$result_SAR_TOT = odbc_exec($connect, $select_SAR_TOT);
$DATI_SAR_TOT = odbc_fetch_object($result_SAR_TOT);

// UPDATE: Aggiorno i campi dell'accordato/utilizzato/depositi TOTALE BANCA
$update_query =  "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.BA_ACCORDATO_TOTALE = '".$DATI_SAR_TOT->TOTALE_ACCORDATO."',
						SDC.BA_UTILIZZATO_TOTALE = '".$DATI_SAR_TOT->TOTALE_UTILIZZATO."',
						SDC.BA_DEPOSITI_TOTALE = '".$DATI_SAR_TOT->TOTALE_DEPOSITI."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 19. ACCORDATO/UTILIZZATO/DEPOSITI SOCI
//
////////////////////////////////////////////////////////////////////////

$select_SAR_SOCI = "	SELECT
							SUM(QUERY1.TMP_TOTALE_ACCORDATO) AS TOTALE_ACCORDATO_SOCI,
							SUM(QUERY1.TMP_TOTALE_UTILIZZATO) AS TOTALE_UTILIZZATO_SOCI,
							SUM(QUERY1.TMP_TOTALE_DEPOSITI) AS TOTALE_DEPOSITI_SOCI
							
						FROM

							(SELECT
								anag.NAG,
								Sum(sar.TOTALE_ACCORDATO) AS TMP_TOTALE_ACCORDATO,
								Sum(sar.TOTALE_UTILIZZATO) AS TMP_TOTALE_UTILIZZATO,
								Sum(sar.TOTALE_DEPOSITI) AS TMP_TOTALE_DEPOSITI,
								sar.ANOM_INT_100,
								sar.ANOM_INT_101
							FROM
								SAR_DATI_POSIZIONE AS sar
							JOIN
								ANAG_NAG AS anag ON anag.NAG = sar.NAG
							JOIN
								SOCI_ANAGRAFICA AS anagsoci ON anagsoci.NAG = sar.NAG								
							WHERE
								sar.ANOM_INT_100 = ''
							AND
								anag.STATO_NAG = '1'
							AND 
								sar.ANNO_MESE_RIF = '".$ANNOMESERIF."'
							AND
								anagsoci.DATA_USCITA = '00/00/0000'
							GROUP BY
								anag.NAG, sar.ANOM_INT_100, sar.ANOM_INT_101) AS QUERY1";

$result_SAR_SOCI = odbc_exec($connect, $select_SAR_SOCI);
$DATI_SAR_SOCI = odbc_fetch_object($result_SAR_SOCI);

// UPDATE: Aggiorno i campi dell'accordato/utilizzato/depositi TOTALE SOCI e NON SOCI
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_ACCORDATO_TOTALE = '".$DATI_SAR_SOCI->TOTALE_ACCORDATO_SOCI."',
						SDC.SO_UTILIZZATO_TOTALE = '".$DATI_SAR_SOCI->TOTALE_UTILIZZATO_SOCI."',
						SDC.SO_DEPOSITI_TOTALE = '".$DATI_SAR_SOCI->TOTALE_DEPOSITI_SOCI."',
						SDC.NS_ACCORDATO_TOTALE = '".($DATI_SAR_TOT->TOTALE_ACCORDATO - $DATI_SAR_SOCI->TOTALE_ACCORDATO_SOCI)."',
						SDC.NS_UTILIZZATO_TOTALE = '".($DATI_SAR_TOT->TOTALE_UTILIZZATO - $DATI_SAR_SOCI->TOTALE_UTILIZZATO_SOCI)."',
						SDC.NS_DEPOSITI_TOTALE = '".($DATI_SAR_TOT->TOTALE_DEPOSITI - $DATI_SAR_SOCI->TOTALE_DEPOSITI_SOCI)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

////////////////////////////////////////////////////////////////////////
//
// 20. SOCI: ACCORDATO/UTILIZZATO/DEPOSITI PF e PG 
//
////////////////////////////////////////////////////////////////////////

$select_SAR_SOCI_PF = "	SELECT
							SUM(QUERY1.TMP_TOTALE_ACCORDATO) AS TOTALE_ACCORDATO_SOCI_PF,
							SUM(QUERY1.TMP_TOTALE_UTILIZZATO) AS TOTALE_UTILIZZATO_SOCI_PF,
							SUM(QUERY1.TMP_TOTALE_DEPOSITI) AS TOTALE_DEPOSITI_SOCI_PF
							
						FROM

							(SELECT
								anag.NAG,
								Sum(sar.TOTALE_ACCORDATO) AS TMP_TOTALE_ACCORDATO,
								Sum(sar.TOTALE_UTILIZZATO) AS TMP_TOTALE_UTILIZZATO,
								Sum(sar.TOTALE_DEPOSITI) AS TMP_TOTALE_DEPOSITI,
								sar.ANOM_INT_100,
								sar.ANOM_INT_101
							FROM
								SAR_DATI_POSIZIONE AS sar
							JOIN
								ANAG_NAG AS anag ON anag.NAG = sar.NAG
							JOIN
								SOCI_ANAGRAFICA AS anagsoci ON anagsoci.NAG = sar.NAG								
							WHERE
								sar.ANOM_INT_100 = ''
							AND
								anag.STATO_NAG = '1'
							AND 
								sar.ANNO_MESE_RIF = '".$ANNOMESERIF."'
							AND
								anagsoci.DATA_USCITA = '00/00/0000'
							AND
								anag.TIPO_NAG = 'PF'
							GROUP BY
								anag.NAG, sar.ANOM_INT_100, sar.ANOM_INT_101) AS QUERY1";

$result_SAR_SOCI_PF = odbc_exec($connect, $select_SAR_SOCI_PF);
$DATI_SAR_SOCI_PF = odbc_fetch_object($result_SAR_SOCI_PF);

// UPDATE: Aggiorno i campi dell'accordato/utilizzato/depositi TOTALE SOCI PF e PG
$update_query = "	UPDATE
						SDS_SOCI_DATI_CONSOLIDATI AS SDC
					SET
						SDC.SO_PF_ACCORDATO_TOTALE = '".$DATI_SAR_SOCI_PF->TOTALE_ACCORDATO_SOCI_PF."',
						SDC.SO_PF_UTILIZZATO_TOTALE = '".$DATI_SAR_SOCI_PF->TOTALE_UTILIZZATO_SOCI_PF."',
						SDC.SO_PF_DEPOSITI_TOTALE = '".$DATI_SAR_SOCI_PF->TOTALE_DEPOSITI_SOCI_PF."',
						SDC.SO_PG_ACCORDATO_TOTALE = '".($DATI_SAR_SOCI->TOTALE_ACCORDATO_SOCI - $DATI_SAR_SOCI_PF->TOTALE_ACCORDATO_SOCI_PF)."',
						SDC.SO_PG_UTILIZZATO_TOTALE = '".($DATI_SAR_SOCI->TOTALE_UTILIZZATO_SOCI - $DATI_SAR_SOCI_PF->TOTALE_UTILIZZATO_SOCI_PF)."',
						SDC.SO_PG_DEPOSITI_TOTALE = '".($DATI_SAR_SOCI->TOTALE_DEPOSITI_SOCI - $DATI_SAR_SOCI_PF->TOTALE_DEPOSITI_SOCI_PF)."'
					WHERE
						SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);
/*
////////////////////////////////////////////////////////////////////////
//
// 21. QUESTIONARI MIFID SOCI: APPROPRIATEZZA
// Definisco le fasce di punteggio di APPROPRIATEZZA (campo CLI_APP)
// della tabella TIT_TDATI_QUESTIONARIO
// in un Array con ($FASCIA => $PUNTEGGIO_DI_PARTENZA|$PUNTEGGIO_DI_FINE)
//
////////////////////////////////////////////////////////////////////////

$ARRAY_FASCE = array (	'1' => '3|11',
						'2' => '12|20',
						'3' => '21|28');

// FOREACH: Per ogni fascia definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FASCE AS $FASCIA => $tmp_punt) {

	$RANGE = explode('|', $tmp_punt);

	$select_MIFID_APP = "	SELECT
								COUNT(anagsoci.IDSOCIO) AS SOCI_MIFID_APP
							FROM 
								SOCI_ANAGRAFICA AS anagsoci
							JOIN
								TIT_TDATI_QUESTIONARIO AS quest ON anagsoci.NAG = quest.NAG
							WHERE
								anagsoci.DATA_USCITA = '00/00/0000'
							AND
								quest.CLI_APP BETWEEN '".$RANGE[0]."' AND '".$RANGE[1]."'";

	$result_MIFID_APP = odbc_exec($connect, $select_MIFID_APP);
	$DATI_MIFID_APP = $result_MIFID_APP->fetch_object();

	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_MIFID_APP_FASCIA".$FASCIA." = '".$DATI_MIFID_APP->SOCI_MIFID_APP."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

$esegui = mysqli_query($connection, $update_query);

}

////////////////////////////////////////////////////////////////////////
//
// 22. QUESTIONARI MIFID SOCI: ADEGUATEZZA
// Definisco le fasce di punteggio di ADEGUATEZZA (campo CLI_ADE)
// della tabella TIT_TDATI_QUESTIONARIO
// in un Array con ($FASCIA => $PUNTEGGIO_DI_PARTENZA|$PUNTEGGIO_DI_FINE)
//
////////////////////////////////////////////////////////////////////////

$ARRAY_FASCE = array (	'1' => '5|16',
						'2' => '17|28',
						'3' => '29|40');

// FOREACH: Per ogni fascia definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FASCE AS $FASCIA => $tmp_punt) {

	$RANGE = explode('|', $tmp_punt);

	$select_MIFID_ADE = "	SELECT
								COUNT(anagsoci.IDSOCIO) AS SOCI_MIFID_ADE
							FROM 
								SOCI_ANAGRAFICA AS anagsoci
							JOIN
								TIT_TDATI_QUESTIONARIO AS quest ON anagsoci.NAG = quest.NAG
							WHERE
								anagsoci.DATA_USCITA = '00/00/0000'
							AND
								quest.CLI_ADE BETWEEN '".$RANGE[0]."' AND '".$RANGE[1]."'";

	$result_MIFID_ADE = odbc_exec($connect, $select_MIFID_ADE);
	$DATI_MIFID_ADE = $result_MIFID_ADE->fetch_object();

	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = mysqli_query($connection, "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_MIFID_ADE_FASCIA".$FASCIA." = '".$DATI_MIFID_ADE->SOCI_MIFID_ADE."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	odbc_exec($connect, $update_query);

}

////////////////////////////////////////////////////////////////////////
//
// 23. QUESTIONARI MIFID SOCI: DOMANDA B5 (DISCRIMINANTE APPROPRIATEZZA)
// Estraggo dalla tabella delle risposte ai questionari MIFID
// TIT_TQUESTIONARIO
// le quantità di risposte alla domanda cod. 306 (B5 sul questionario)
// tramite un Array con ($FASCIA_RISPOSTA => $COD_DOMANDA|$COD_RISPOSTA)
//
////////////////////////////////////////////////////////////////////////

$ARRAY_FASCE = array (	'1' => '306|346',
						'2' => '306|347',
						'3' => '306|345',
						'4' => '306|348',
						'5' => '306|349');


// FOREACH: Per ogni fascia definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FASCE AS $FASCIA => $tmp_punt) {

	$DOMANDARISPOSTA = explode('|', $tmp_punt);

	$select_MIFID_B5 = "	SELECT COUNT(*) AS MIFID_B5
									FROM (

										SELECT 
											DISTINCT(anagsoci.NAG) 
										FROM 
											SOCI_ANAGRAFICA AS anagsoci
										JOIN
											TIT_TQUESTIONARIO AS quest ON anagsoci.NAG = quest.NAG
										WHERE
											anagsoci.DATA_USCITA = '00/00/0000'
										AND 
											quest.CODICE_DOMANDA = '".$DOMANDARISPOSTA[0]."' 
										AND 
											quest.CODICE_RISPOSTA = '".$DOMANDARISPOSTA[1]."'

									) AS QUERY_EXT";

	$result_MIFID_B5 = odbc_exec($connect, $select_MIFID_B5);
	$DATI_MIFID_B5 = $result_MIFID_B5->fetch_object();

	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = mysqli_query($connection, "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_DOMANDA_B5_RISP_".$FASCIA." = '".$DATI_MIFID_B5->MIFID_B5."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	odbc_exec($connect, $update_query);

}

////////////////////////////////////////////////////////////////////////
//
// 24. QUESTIONARI MIFID SOCI: DOMANDA D2 (DISCRIMINANTE ADEGUATEZZA)
// Estraggo dalla tabella delle risposte ai questionari MIFID
// TIT_TQUESTIONARIO
// le quantità di risposte alla domanda cod. 316 (D2 sul questionario)
// tramite un Array con ($FASCIA_RISPOSTA => $COD_DOMANDA|$COD_RISPOSTA)
//
////////////////////////////////////////////////////////////////////////

$ARRAY_FASCE = array (	'1' => '316|305',
						'2' => '316|306',
						'3' => '316|307',
						'4' => '316|308');


// FOREACH: Per ogni fascia definita nell'array, faccio la query 
// ed estraggo i dati dei soci
foreach ($ARRAY_FASCE AS $FASCIA => $tmp_punt) {

	$DOMANDARISPOSTA = explode('|', $tmp_punt);

	$select_MIFID_D2 = "	SELECT COUNT(*) AS MIFID_D2
									FROM (

										SELECT 
											DISTINCT(anagsoci.NAG) 
										FROM 
											SOCI_ANAGRAFICA AS anagsoci
										JOIN
											TIT_TQUESTIONARIO AS quest ON anagsoci.NAG = quest.NAG
										WHERE
											anagsoci.DATA_USCITA = '00/00/0000'
										AND 
											quest.CODICE_DOMANDA = '".$DOMANDARISPOSTA[0]."' 
										AND 
											quest.CODICE_RISPOSTA = '".$DOMANDARISPOSTA[1]."'

									) AS QUERY_EXT";

	$result_MIFID_D2 = odbc_exec($connect, $select_MIFID_D2);
	$DATI_MIFID_D2 = $result_MIFID_D2->fetch_object();

	// UPDATE: Aggiorno il campo del numero dei SOCI appartenenti alla fascia analizzata
	$update_query = mysqli_query($connection, "	UPDATE
							SDS_SOCI_DATI_CONSOLIDATI AS SDC
						SET
							SDC.SO_DOMANDA_D2_RISP_".$FASCIA." = '".$DATI_MIFID_D2->MIFID_D2."'
						WHERE
							SDC.id_soci_dati_consolidati = '".$LAST_ID."'";

	odbc_exec($connect, $update_query);

}
*/
/*
// ----------------------
// Chiusura ed invio Mail
// ----------------------
$orario2 = date("H:i:s");

$mail_corpo .= 'Inserita riga ID '.$LAST_ID;

$mail_corpo .= '<br><br> Orario fine : '.$orario2;                       
$mail_corpo .= "<br><small>Questa mail viene generata in maniera automatica. In caso di problemi, contattare Ufficio Soci.</small>";
$mail_corpo .= "</body></html>\r\n";

      $mail_headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
      $mail_headers .= "Reply-To: " . $mail_mittente . "\r\n";
      $mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
      $mail_headers .= "MIME-Version: 1.0\r\n";
      $mail_headers .= "Content-type: text/html; charset=UTF-8\r\n";
      $mail_headers .= "Content-Transfer-Encoding: base64";
      //$mail_headers .= "CC: ".$mail_cc."\r\n";
      
      //$mail_oggetto_encoded = '=?UTF-8?B?' . base64_encode($mail_oggetto) . '?=';
      $mail_corpo_encoded = base64_encode($mail_corpo);

      if (mail($mail_dest, $mail_oggetto, $mail_corpo_encoded, $mail_headers)) {
        echo "<center>Messaggio inviato a " . $mail_dest . "<br />\r\n";
      } else {
        echo "<center>Errore. Nessun messaggio inviato. <br />\r\n";
      }

echo $mail_corpo;

*/

      // ---- Aggiorno la tabella ULTIMO_CARICAMENTO
      $updcaricamento = "     UPDATE tab_ultimo_caricamento
                              SET   caricamento=now(), fonte='SDS_SOCI_DATI_CONSOLIDATI' 
                              WHERE fonte='SDS_SOCI_DATI_CONSOLIDATI'
                        ";
      $querydati_updcaricamento = mysqli_query($connection, $updcaricamento);     
      
?>