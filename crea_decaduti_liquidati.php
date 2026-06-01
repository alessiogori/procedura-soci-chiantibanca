<?php
//////////////////////////////////////////////////////////////////
// CREA TABELLA TAB_PREVISIONALE
// Viene richiamato nello scrito CREA_SDS_SOCI.PHP
// Author: Alessio Fedi - 23.13.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'CREA_TAB_DECADUTI_LIQUIDATI';
$TITOLO = 'Soci da Liquidare';

include("config/_config.php");

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Connessione a MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------
$adesso = date("d.m.Y");
$adesso_anno = date("Y");
$iniziodecadenza = $adesso_anno - 1;

// -------------------------------------------------------------------------------
// Sego via tutti i records nella tabella temporanea TMP_PREVISIONALE
// -------------------------------------------------------------------------------
$truncatetabella_tab_previsionale = mysqli_query($connection,"TRUNCATE TAB_DECADUTI_LIQUIDATI") 
									or die(mysqli_error($connection));
      
// -------------------------------------------------------------------------------
// e ricreo tutto il set di dati di dettaglio in TMP_PREVISIONALE in modalità FULL
// -------------------------------------------------------------------------------
$select =
    "SELECT
        d.NAG,
        d.IDSOCIO,
        d.NOMINATIVO,
        d.DATA_ENTRATA,
        d.DATA_MOVIMENTO as DATA_MOVIMENTO_DECADENZA,
        d.DATA_DECADENZA,
        l.DATA_MOVIMENTO AS DATA_MOVIMENTO_LIQUIDAZIONE,
        l.DATA_DELIBERA,
        l.DATA_PAGAMENTO,
        d.FILIALE_CAPOFILA AS FILIALE,
        l.IMPORTO,
        l.SOVRAPPREZZO,
        d.TIPOLOGIA_USCITA,
        e.Escluso_x_Passaggio_a_Sofferenze AS SOFFERENZA
    FROM
        view_decaduti d
            JOIN view_decaduti_liquidati l 
            ON d.IDSOCIO = l.IDSOCIO
            LEFT JOIN tab_xls_esclusioni e 
            ON d.IDSOCIO = e.IDSOCIO
    GROUP BY
        d.NAG, d.IDSOCIO
    ORDER BY
        d.IDSOCIO
    ";

//echo $select;
$query = mysqli_query($connection, $select);
while($dati = mysqli_fetch_array($query)){ 

    // Vado a cercare la motivazione della MORTE, per scindere RIMBORSI da INTESTAZIONE EREDI
    if ($dati['TIPOLOGIA_USCITA'] == 'MORTE')
    { $selectTipoMorte 
              = "SELECT m.CTIPOMOV 
                 FROM sds_soci_movinout as m, view_decaduti_liquidati as l 
                 WHERE m.IDSOCIO = ".$dati['IDSOCIO']."
                 AND m.IDSOCIO = l.IDSOCIO
                AND str_to_date(m.DATA_MOVIMENTO,'%d/%m/%Y') = str_to_date(l.DATA_MOVIMENTO,'%Y-%m-%d')
                AND str_to_date(m.DATA_DELIBERA,'%d/%m/%Y') = str_to_date(l.DATA_DELIBERA,'%Y-%m-%d')
                 ";

        $queryTipoMorte = mysqli_query($connection, $selectTipoMorte);
        while($datiTipoMorte = mysqli_fetch_array($queryTipoMorte)){ 

        // Inserisco i MORTI
          $select_insert = "
                INSERT INTO TAB_DECADUTI_LIQUIDATI
                VALUES 
               (
                 '".$dati['NAG']."'
                ,'".$dati['IDSOCIO']."'
                ,'".mysqli_real_escape_string($connection,$dati['NOMINATIVO'])."'
                ,'".$dati['DATA_ENTRATA']."'
                ,'".$dati['DATA_MOVIMENTO_DECADENZA']."'
                ,'".$dati['DATA_DECADENZA']."'
                ,'".$dati['DATA_MOVIMENTO_LIQUIDAZIONE']."'
                ,'".$dati['DATA_DELIBERA']."'
                ,'".$dati['DATA_PAGAMENTO']."'
                ,'".$dati['FILIALE']."'
                ,'".$dati['IMPORTO']."'
                ,'".$dati['SOVRAPPREZZO']."'
                ,'".$dati['TIPOLOGIA_USCITA']."'
                ,'".$dati['SOFFERENZA']."'
                ,'".$datiTipoMorte['CTIPOMOV']."'
                )
                ";
    //echo $select_insert;
            mysqli_query($connection, $select_insert )
                        or die("INSERT --- ".mysqli_error($connection));;
          }
    }
    else 
    {
        // Inserisco le ESCLUSIONI e i RECESSI
          $select_insert = "
                INSERT INTO TAB_DECADUTI_LIQUIDATI
                VALUES 
               (
                 '".$dati['NAG']."'
                ,'".$dati['IDSOCIO']."'
                ,'".mysqli_real_escape_string($connection,$dati['NOMINATIVO'])."'
                ,'".$dati['DATA_ENTRATA']."'
                ,'".$dati['DATA_MOVIMENTO_DECADENZA']."'
                ,'".$dati['DATA_DECADENZA']."'
                ,'".$dati['DATA_MOVIMENTO_LIQUIDAZIONE']."'
                ,'".$dati['DATA_DELIBERA']."'
                ,'".$dati['DATA_PAGAMENTO']."'
                ,'".$dati['FILIALE']."'
                ,'".$dati['IMPORTO']."'
                ,'".$dati['SOVRAPPREZZO']."'
                ,'".$dati['TIPOLOGIA_USCITA']."'
                ,'".$dati['SOFFERENZA']."'
                ,''
                )
                ";
    //echo $select_insert;
            mysqli_query($connection, $select_insert )
                        or die("INSERT --- ".mysqli_error($connection));;


    }


} // Fine while

// ---- Aggiorno la tabella ULTIMO_CARICAMENTO
$updcaricamento = "     UPDATE tab_ultimo_caricamento
                      SET   caricamento=now(), fonte='TAB_DECADUTI_LIQUIDATI' 
                      WHERE fonte='TAB_DECADUTI_LIQUIDATI'
                ";
$querydati_updcaricamento = mysqli_query($connection, $updcaricamento);     
		