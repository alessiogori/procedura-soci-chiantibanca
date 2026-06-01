<?php
//////////////////////////////////////////////////////////////////
// CREA TABELLA TAB_PREVISIONALE
// Viene richiamato nello scrito CREA_SDS_SOCI.PHP
// Author: Alessio Fedi - 23.13.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'CREA_TAB_DECADUTI_NONLIQUIDATI';
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
$truncatetabella_tab_previsionale = mysqli_query($connection,"TRUNCATE TAB_DECADUTI_NONLIQUIDATI") 
									or die(mysqli_error($connection));
      
// -------------------------------------------------------------------------------
// e ricreo tutto il set di dati di dettaglio in TMP_PREVISIONALE in modalità FULL
// -------------------------------------------------------------------------------
$select = 
        "SELECT 
            NAG,
            IDSOCIO,
            NOMINATIVO,  
            DATA_ENTRATA,
            DATA_DECADENZA,
            FILIALE_CAPOFILA FILIALE,
            TOTALE_AZIONI NUMERO_AZIONI,
            VALORE_AZIONI IMPORTO,
            TIPOLOGIA_USCITA,
            SOFFERENZA
         FROM VIEW_DECADUTI_NONLIQUIDATI_CON_SOFFERENZE
         ORDER BY DATA_DECADENZA ASC        
        ";

$query = mysqli_query($connection, $select);
while($dati = mysqli_fetch_array($query)){ 

      $select_insert = "
            INSERT INTO TAB_DECADUTI_NONLIQUIDATI
            VALUES 
           (
             '".$dati['NAG']."'
            ,'".$dati['IDSOCIO']."'
            ,'".mysqli_real_escape_string($connection,$dati['NOMINATIVO'])."'
            ,'".$dati['DATA_ENTRATA']."'
            ,'".$dati['DATA_DECADENZA']."'
            ,'".$dati['FILIALE']."'
            ,'".$dati['NUMERO_AZIONI']."'
            ,'".$dati['IMPORTO']."'
            ,'".$dati['TIPOLOGIA_USCITA']."'
            ,'".$dati['SOFFERENZA']."'
            )
            ";

        mysqli_query($connection, $select_insert )
                    or die("INSERT --- ".mysqli_error($connection));;
      }


// ---- Aggiorno la tabella ULTIMO_CARICAMENTO
$updcaricamento = "     UPDATE tab_ultimo_caricamento
                      SET   caricamento=now(), fonte='TAB_DECADUTI_NONLIQUIDATI' 
                      WHERE fonte='TAB_DECADUTI_NONLIQUIDATI'
                ";
$querydati_updcaricamento = mysqli_query($connection, $updcaricamento);     
		