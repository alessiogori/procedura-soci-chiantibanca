<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
//
// Questo script serve a controllare se fossero presenti nel db CESSIONI
// posizioni che in realtà sono già estinte
//
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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>

<?php		
if (empty($_GET['filiale'])) 
{$condizionefiliale = "WHERE filiale <> 999 ";}
else 
{$condizionefiliale = " WHERE filiale = ".$_GET['filiale']." ";}


if ($_GET['scelta']=="nomail") {
    
    // SOCI SENZA MAIL
    // ------------------------------------------------------------------------
    
	$contenutomail = '';
    $select_cnt1a = "
                    SELECT *
                    FROM tab_soci_as37
                    WHERE indirizzoEMail = ''
                    AND StatoVAL not in ('E','S')
                    " ;
    
    $qry_cnt1a = mysqli_query($connection, $select_cnt1a);
    $myfilemail = fopen("tmp/socisenzaemail.csv", "w");
    $contenutomail .= "stato;cag;int1Socio;int2Socio;codFil;int1Filiale;telefono;Email;PEC\n";
    while($cnt1a = mysqli_fetch_array($qry_cnt1a)){ 
	    $contenutomail .= $cnt1a['stato'].";".$cnt1a['cag'].";".$cnt1a['int1Socio'].";".$cnt1a['int2Socio'].";".$cnt1a['codFil'].";".$cnt1a['int1Filiale'].";".$cnt1a['telefono'].";".$cnt1a['indirizzoEMail'].";".$cnt1a['indirizzoPEC']."\n";
	}
    fwrite($myfilemail, $contenutomail);
    fclose($myfilemail);
    
    echo '<body onload="window.open(\'tmp/socisenzaemail.csv\'); history.back();"> ';

    
}


elseif ($_GET['scelta']=="dipendenti") {

    // SOCI DIPENDENTI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_cnt2a = "select a.nag, concat(trim(a.intestazione_a), ' ', trim(a.intestazione_b)) as dipendente, 
                    a.FILIALE_CAPOFILA as FilialeAnag,
                    sum(c.nazioni) as NumAzioni, sum(c.nazioni)*30.33 as ValoreAzioni
                    from sds_anag_nag a, sds_soci_anagrafica s, sds_soci_certificati c
                    where a.SEGMENTO_CLIENTE = 18
                    and (c.DATA_VENDITA = 0 and c.DATA_ANNULLAMENTO = 0)
                    and a.NAG = s.NAG
                    and s.IDSOCIO = c.IDSOCIO
                    group by a.nag, a.intestazione_a, a.intestazione_b
                    order by 2
                    " ;
    
    $qry_cnt2a = mysqli_query($connection, $select_cnt2a);
    $myfiledip = fopen("tmp/dipendentisocio.csv", "w");
    $contenutodip .= "nag;dipendente;Filiale;NumAzioni;ValoreAzioni\n";
    while($cnt2a = mysqli_fetch_array($qry_cnt2a)){ 
        $contenutodip .= $cnt2a['nag'].";".$cnt2a['dipendente'].";".$cnt2a['FilialeAnag'].";".$cnt2a['NumAzioni'].";".number_format($cnt2a['ValoreAzioni'],2,',','.')."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/dipendentisocio.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="su94") {

    // SOCI CON CONTO BLOCCATO SU o 94
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_cnt2a = "
                    SELECT  s.prot, d.cag, d.intestazione1, d.filiale, d.codiceblocco, d.rapportoblocco, d.utenteblocco, d.datablocco,
                            s.statoVAL as StatoSocio, s.nAzTot, s.nominaleAzTot 
                    FROM tab_deceduti as d, tab_soci_as37 as s
                    WHERE s.tipoContropVAL = 11000
                    AND s.statoVAL not in ('E','S')
                    AND d.cag = s.cag
                    GROUP BY d.cag
                    ORDER BY 3,2
                    " ;
    
    $qry_cnt2a = mysqli_query($connection, $select_cnt2a);
    $myfiledip = fopen("tmp/decessisu94.csv", "w");
    $contenutodip .= "Prot;CAG;Intestazione;Filiale;CodiceBlocco;RapportoBlocco;UtenteBlocco;DataBlocco;StatoSocio;AzTot;NominaleAzTot\n";
    while($cnt2a = mysqli_fetch_array($qry_cnt2a)){ 
        $contenutodip .= $cnt2a['prot'].";".$cnt2a['cag'].";".$cnt2a['intestazione1'].";".$cnt2a['filiale'].";"
                        .$cnt2a['codiceblocco'].";".$cnt2a['rapportoblocco'].";".$cnt2a['utenteblocco'].";".$cnt2a['datablocco'].";"
                        .$cnt2a['StatoSocio'].";".$cnt2a['nAzTot'].";".$cnt2a['nominaleAzTot']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/decessisu94.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="pac") {
// ********************************************************
// PAC
// ********************************************************
	$contenutodip = '';
    $strQueryP = "  SELECT prot,
                    CDA, Filiale, a.CAG, Conto, Manca_DB, Nominativo, PrimoAddebito, Azioni_Sottoscritte, Flag_da_SUCC_CESS, 
                    Qta_Azioni_da_Succ_Cess, Qta_Ulteriori_azioni_da_acquistare, Pac, nAzTot, nominaleAzTot, dataEntrata
                    FROM tab_xls_ammissioni as a, tab_soci_as37 as s
                    WHERE Pac ='S' 
                    AND a.CAG = s.cag
                    ORDER BY Filiale, Nominativo   ";
    
    $resultP = mysqli_query($connection, $strQueryP);
    $myfiledip = fopen("tmp/giovani_pac.csv", "w");
    $contenutodip .= "Tipo;Filiale;DataEntrata;DataNextAddebito;CAG;CONTO;Intestazione;nAzTot;nominaleAzTot;AzDaAcquistare\n";

  // iterating over each data and pushing it into $arrData array
  while ($dati_p = mysqli_fetch_array($resultP)) {

    $primoaddebito = $dati_p['dataEntrata'];                    // $primoaddebito = $dati_p['PrimoAddebito'];

			if ($primoaddebito <> '') {                         // if ($dati_p['PrimoAddebito'] <> '') { 
  
	            $QuoteAttuali = $dati_p['nAzTot'];
	            $AddebitiFatti = $QuoteAttuali / 3 ;
	            $QuoteMancanti = 33 - $QuoteAttuali;
	            $AddebitiMancanti = $QuoteMancanti / 3 ;
	            
	            list($giorno, $mese, $anno) = explode("/", $primoaddebito);
	            // mesi da sommare alla data
	            $NM = $AddebitiFatti;
	            // giorni da sommmare alla data
	            $NG = 0;
	            // anni da sommare alla data
	            $NA = 0;
	            // stampo la nuova data risultato della data impostata e dei valori aggiuntivi
	            $prossimoaddebito = date("d/m/Y",mktime(0,0,0,$mese+$NM,30,$anno));  
	            
			}
			else
			{
				$prossimoaddebito = '';
	            $QuoteAttuali = '';
	            $QuoteMancanti = '';
	            $AddebitiMancanti = '';	
			}

            switch ($dati_p['Flag_da_SUCC_CESS']) {
              case 'C':
                $origine = "Cessione";
                break;
              case 'S':
                $origine = "Successione";
                break;
              case 'D':
                $origine = "Donazione";
                break;
              case 'M':
                $origine = "Mutua";
                break;
              case 'R':
                $origine = "Rateiz";
                break;
              default:
                $origine = "";
              }
            

        $contenutodip .= $dati_p['Flag_da_SUCC_CESS'].";".$dati_p['Filiale'].";".$dati_p['dataEntrata'].";".$prossimoaddebito.";A01.".$dati_p['CAG'].";C01/".$dati_p['Conto'].";".$dati_p['Nominativo'].";".$dati_p['nAzTot'].";".$dati_p['nominaleAzTot'].";".$QuoteMancanti."\n";
        
  }

    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/giovani_pac.csv\'); history.back();"> ';

}

elseif ($_GET['scelta']=="inamm") {

    // ------------------------------------------------------------------------
    // DOMANDE IN AMMISSIONE
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_ammissioni = "
                       SELECT CDA, Filiale, CAG, Conto, Nominativo, Data_Domanda, Azioni_Sottoscritte, Flag_da_SUCC_CESS, Note 
                       FROM tab_xls_ammissioni
                       WHERE STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_ammissioni ) OR CDA = 'NEXT'
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_ammissioni = mysqli_query($connection, $select_ammissioni);
    $myfiledip = fopen("tmp/domandeinammissione.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Conto;Nominativo;DataDomanda;AzioniSottoscritte;Origine;Note\n";
    while($ammissioni = mysqli_fetch_array($qry_ammissioni)){ 
        $contenutodip .= $ammissioni['CDA'].";".$ammissioni['Filiale'].";".$ammissioni['CAG'].";".$ammissioni['Conto'].";".$ammissioni['Nominativo'].";".$ammissioni['Data_Domanda'].";".$ammissioni['Azioni_Sottoscritte'].";".$ammissioni['Flag_da_SUCC_CESS'].";".$ammissioni['Note']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandeinammissione.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="sconf") {

    // ------------------------------------------------------------------------
    // DOMANDE SCONFINANTI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_sconf = "
                       SELECT CDA, Filiale, CAG, Conto, Manca_DB, Nominativo, Data_Domanda, Azioni_Sottoscritte, Flag_da_SUCC_CESS, Note 
                       FROM tab_xls_ammissioni
                       WHERE Manca_DB = 'S'
                       AND STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_ammissioni )
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_sconf = mysqli_query($connection, $select_sconf);
    $myfiledip = fopen("tmp/domandesconfinanti.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Conto;Manca_DB;Nominativo;DataDomanda;AzioniSottoscritte;Origine;Note\n";
    while($sconf = mysqli_fetch_array($qry_sconf)){ 
        $contenutodip .= $sconf['CDA'].";".$sconf['Filiale'].";".$sconf['CAG'].";".$sconf['Conto'].";".$sconf['Manca_DB'].";".$sconf['Nominativo'].";".$sconf['Data_Domanda'].";".$sconf['Azioni_Sottoscritte'].";".$sconf['Flag_da_SUCC_CESS'].";".$sconf['Note']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandesconfinanti.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="eredi") {

    // ------------------------------------------------------------------------
    // INTESTAZIONE A EREDI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_eredi = "
                       SELECT CDA, Filiale, CAG, Nominativo, Numero_Azioni, Valore_Nominale, Data_Richiesta, Note_Motivazioni, Data_Decesso
                       FROM tab_xls_decessi_eredi
                       WHERE Intestazione_a_eredi = 'S'
                       AND STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_decessi_eredi ) OR CDA = 'NEXT'
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_eredi = mysqli_query($connection, $select_eredi);
    $myfiledip = fopen("tmp/domandeintestazioneeredi.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Nominativo;NumeroAzioni;ValoreNominale;DataRichiesta;Note;DataDecesso\n";
    while($eredi = mysqli_fetch_array($qry_eredi)){ 
        $contenutodip .= $eredi['CDA'].";".$eredi['Filiale'].";".$eredi['CAG'].";".$eredi['Nominativo'].";".$eredi['Numero_Azioni'].";".$eredi['Valore_Nominale'].";".$eredi['Data_Richiesta'].";".$eredi['Note_Motivazioni'].";".$eredi['Data_Decesso']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandeintestazioneeredi.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="esclus") {

    // ------------------------------------------------------------------------
    // ESCLUSIONI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_dati = "
                       SELECT CDA, Filiale, CAG, Conto, Nominativo, Numero_Azioni, Valore_Nominale, Data_Richiesta, Note_Motivazioni, Accoglimento_recesso, Escluso_art_6, Escluso_art_14
                       FROM tab_xls_recessi_esclusioni_sofferenze
                       WHERE (Escluso_art_6 = 'S' OR Escluso_art_14 = 'S' OR Accoglimento_recesso = 'S' OR Accoglimento_recesso = 'N')
                       AND STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_recessi_esclusioni_sofferenze ) OR CDA = 'NEXT'
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_dati = mysqli_query($connection, $select_dati);
    $myfiledip = fopen("tmp/domandeesclusioni.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Pagamento;Nominativo;NumeroAzioni;ValoreNominale;DataRichiesta;Note;Recesso;Art6;Art14\n";
    while($dati = mysqli_fetch_array($qry_dati)){ 
        $contenutodip .= $dati['CDA'].";".$dati['Filiale'].";".$dati['CAG'].";".$dati['Conto'].";".$dati['Nominativo'].";".$dati['Numero_Azioni'].";".$dati['Valore_Nominale'].";".$dati['Data_Richiesta'].";".$dati['Note_Motivazioni'].";".$dati['Accoglimento_recesso'].";".$dati['Escluso_art_6'].";".$dati['Escluso_art_14']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandeesclusioni.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="cessbanca") {

    // ------------------------------------------------------------------------
    // ESCLUSIONI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_dati = "
                       SELECT CDA, Filiale, CAG, Conto, Nominativo, Numero_Azioni, Valore_Nominale, Data_Richiesta, Note_Motivazioni, Totale_Parziale
                       FROM tab_xls_cessioni
                       WHERE Cessione_a_Banca = 'S' 
                       AND STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_cessioni ) OR CDA = 'NEXT'
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_dati = mysqli_query($connection, $select_dati);
    $myfiledip = fopen("tmp/domandecessionebanca.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Pagamento;Nominativo;NumeroAzioni;ValoreNominale;DataRichiesta;Note;TotaleParziale\n";
    while($dati = mysqli_fetch_array($qry_dati)){ 
        $contenutodip .= $dati['CDA'].";".$dati['Filiale'].";".$dati['CAG'].";".$dati['Conto'].";".$dati['Nominativo'].";".$dati['Numero_Azioni'].";".$dati['Valore_Nominale'].";".$dati['Data_Richiesta'].";".$dati['Note_Motivazioni'].";".$dati['Totale_Parziale']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandecessionebanca.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="cesssocio") {

    // ------------------------------------------------------------------------
    // ESCLUSIONI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_dati = "
                       SELECT CDA, Filiale, CAG, Nominativo, Numero_Azioni, Valore_Nominale, Data_Richiesta, Note_Motivazioni, Totale_Parziale
                       FROM tab_xls_cessioni
                       WHERE Cessione_a_Socio = 'S' 
                       AND STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_cessioni ) OR CDA = 'NEXT'
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_dati = mysqli_query($connection, $select_dati);
    $myfiledip = fopen("tmp/domandecessionesocio.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Nominativo;NumeroAzioni;ValoreNominale;DataRichiesta;Note;TotaleParziale\n";
    while($dati = mysqli_fetch_array($qry_dati)){ 
        $contenutodip .= $dati['CDA'].";".$dati['Filiale'].";".$dati['CAG'].";".$dati['Nominativo'].";".$dati['Numero_Azioni'].";".$dati['Valore_Nominale'].";".$dati['Data_Richiesta'].";".$dati['Note_Motivazioni'].";".$dati['Totale_Parziale']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandecessionesocio.csv\'); history.back();"> ';


}

elseif ($_GET['scelta']=="ultacqaz") {

    // ------------------------------------------------------------------------
    // ESCLUSIONI
    // ------------------------------------------------------------------------
	$contenutodip = '';
    $select_dati = "
                       SELECT CDA, Filiale, CAG, Conto, Nominativo, Numero_Azioni, Valore_Nominale, Data_Richiesta, Azioni_Possedute, Totale_Azioni
                       FROM tab_xls_acquistoulterioriazioni
                       WHERE STR_TO_DATE(CDA, '%d/%m/%Y') = ( select max(STR_TO_DATE(CDA, '%d/%m/%Y')) 
                       FROM tab_xls_acquistoulterioriazioni ) OR CDA = 'NEXT'
                       ORDER BY Filiale, Nominativo
                    " ;
    
    $qry_dati = mysqli_query($connection, $select_dati);
    $myfiledip = fopen("tmp/domandeulterioriazioni.csv", "w");
    $contenutodip .= "CDA;Filiale;CAG;Conto;Nominativo;NumeroAzioni;ValoreNominale;DataRichiesta;AzioniPossedute;TotaleAzioni\n";
    while($dati = mysqli_fetch_array($qry_dati)){ 
        $contenutodip .= $dati['CDA'].";".$dati['Filiale'].";".$dati['CAG'].";".$dati['Conto'].";".$dati['Nominativo'].";".$dati['Numero_Azioni'].";".$dati['Valore_Nominale'].";".$dati['Data_Richiesta'].";".$dati['Azioni_Possedute'].";".$dati['Totale_Azioni']."\n";
	}
    fwrite($myfiledip, $contenutodip);
    fclose($myfiledip);

    echo '<body onload="window.open(\'tmp/domandeulterioriazioni.csv\'); history.back();"> ';


}


else {}

?>