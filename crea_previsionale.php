<?php
//////////////////////////////////////////////////////////////////
// CREA TABELLA TAB_PREVISIONALE
// Viene richiamato nello scrito CREA_SDS_SOCI.PHP
// Author: Alessio Fedi - 23.13.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'CREA_TAB_PREVISIONALE';
$TITOLO = 'Dati Uscite Area e Filiali con Soci necessari al pareggio';

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
$truncatetabella_tab_previsionale = mysqli_query($connection,"TRUNCATE TAB_PREVISIONALE") 
									or die(mysqli_error($connection));
      
// -------------------------------------------------------------------------------
// e ricreo tutto il set di dati di dettaglio in TMP_PREVISIONALE in modalità FULL
// -------------------------------------------------------------------------------
$select_tmp_previsionale_full = 
        "SELECT
            Area,
            p.Filiale, t.desc_filiale as NomeFiliale,
            sum(CASE WHEN Tipo = 'ESCLUSIONE' THEN round(Importo,0) ELSE null END) as ESCLUSIONE,
            sum(CASE WHEN Tipo = 'ESCLUSIONE SOFFERENZA' THEN round(Importo,0) ELSE null END) as ESCLUSIONE_SOFFERENZA,
            sum(CASE WHEN Tipo = 'RECESSO' THEN round(Importo,0) ELSE null END) as RECESSO,
            sum(CASE WHEN Tipo = 'MORTE' THEN round(Importo,0) ELSE null END) as MORTE,
            sum(CASE WHEN Tipo = 'CESSIONE A BANCA' THEN round(Importo,0) ELSE null END) as CESSIONE_BANCA,

            /* Totale per Filiale */
            (   SELECT round(sum(Importo))
                FROM view_previsionale_full p1
                WHERE p.Filiale = p1.Filiale
                GROUP BY p1.Filiale) as TOTALE,

            /* Totale : 30,09 : 33 (quantità Soci necessari a ripianare le uscite) */
            round((((   SELECT round(sum(Importo))
                FROM view_previsionale_full p1
                WHERE p.Filiale = p1.Filiale
                GROUP BY p1.Filiale) 
            / 30.33) / 33) ,0)
            as NUMERO_SOCI

            FROM view_previsionale_full p, tab_psw t
            WHERE p.Filiale = cast(t.Filiale as unsigned)
            AND p.Filiale <> 'Banca'
            GROUP BY p.Filiale
            ORDER BY cast(p.Filiale as unsigned)        
        ";

$query_full = mysqli_query($connection, $select_tmp_previsionale_full);
while($dati_full = mysqli_fetch_array($query_full)){ 

      $select_insert_full = "
            INSERT INTO TAB_PREVISIONALE
            VALUES 
           (
             'FULL'
            ,'".$dati_full['Area']."'
            ,'".mysqli_real_escape_string($connection,$dati_full['Filiale'])."'
            ,'".mysqli_real_escape_string($connection,$dati_full['NomeFiliale'])."'
            ,'".$dati_full['ESCLUSIONE']."'
            ,'".$dati_full['ESCLUSIONE_SOFFERENZA']."'
            ,'".$dati_full['RECESSO']."'
            ,'".$dati_full['MORTE']."'
            ,'".$dati_full['CESSIONE_BANCA']."'
            ,'".$dati_full['TOTALE']."'
            ,'".$dati_full['NUMERO_SOCI']."'
            )
            ";

        mysqli_query($connection, $select_insert_full )
                    or die("INSERT --- ".mysqli_error($connection));;
      }

// ---------------------------------------------------------------------------------------------------
// ricreo tutto il set di dati di dettaglio in TMP_PREVISIONALE in modalità LIMITATA pre-anno in corso
// ---------------------------------------------------------------------------------------------------
$select_tmp_previsionale = 
        "SELECT
            Area,
            p.Filiale, t.desc_filiale as NomeFiliale,
            sum(CASE WHEN Tipo = 'ESCLUSIONE' THEN round(Importo,0) ELSE null END) as ESCLUSIONE,
            sum(CASE WHEN Tipo = 'ESCLUSIONE SOFFERENZA' THEN round(Importo,0) ELSE null END) as ESCLUSIONE_SOFFERENZA,
            sum(CASE WHEN Tipo = 'RECESSO' THEN round(Importo,0) ELSE null END) as RECESSO,
            sum(CASE WHEN Tipo = 'MORTE' THEN round(Importo,0) ELSE null END) as MORTE,
            sum(CASE WHEN Tipo = 'CESSIONE A BANCA' THEN round(Importo,0) ELSE null END) as CESSIONE_BANCA,

            /* Totale per Filiale */
            (   SELECT round(sum(Importo))
                FROM view_previsionale p1
                WHERE p.Filiale = p1.Filiale
                GROUP BY p1.Filiale) as TOTALE,

            /* Totale : 30,09 : 33 (quantità Soci necessari a ripianare le uscite) */
            round((((   SELECT round(sum(Importo))
                FROM view_previsionale p1
                WHERE p.Filiale = p1.Filiale
                GROUP BY p1.Filiale) 
            / 30.33) / 33) ,0)
            as NUMERO_SOCI

            FROM view_previsionale p, tab_psw t
            WHERE p.Filiale = cast(t.Filiale as unsigned)
            AND p.Filiale <> 'Banca'
            GROUP BY p.Filiale
            ORDER BY cast(p.Filiale as unsigned)        
        ";

$query = mysqli_query($connection, $select_tmp_previsionale);
while($dati = mysqli_fetch_array($query)){ 

      $select_insert = "
            INSERT INTO TAB_PREVISIONALE
            VALUES 
           (
             'LIMIT'
            ,'".$dati['Area']."'
            ,'".mysqli_real_escape_string($connection,$dati['Filiale'])."'
            ,'".mysqli_real_escape_string($connection,$dati['NomeFiliale'])."'
            ,'".$dati['ESCLUSIONE']."'
            ,'".$dati['ESCLUSIONE_SOFFERENZA']."'
            ,'".$dati['RECESSO']."'
            ,'".$dati['MORTE']."'
            ,'".$dati['CESSIONE_BANCA']."'
            ,'".$dati['TOTALE']."'
            ,'".$dati['NUMERO_SOCI']."'
            )
            ";

        mysqli_query($connection, $select_insert )
                    or die("INSERT --- ".mysqli_error($connection));;
      }


// ---- Aggiorno la tabella ULTIMO_CARICAMENTO
$updcaricamento = "     UPDATE tab_ultimo_caricamento
                      SET   caricamento=now(), fonte='TAB_PREVISIONALE' 
                      WHERE fonte='TAB_PREVISIONALE'
                ";
$querydati_updcaricamento = mysqli_query($connection, $updcaricamento);     
		
include ("routines/mail_dip.php");
