<?php
//////////////////////////////////////////////////////////////////
// CREA TABELLA SDS_SOCI_IMPIEGHIRACCOLTA
// Viene richiamato nello scrito CREA_SDS_SOCI.PHP
// Author: Alessio Fedi - 23.13.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'CREA_SDS_SOCI_IMPIEGHIRACCOLTA';
$TITOLO = 'Dati Statistici per Impieghi/Raccolta/Numero Rapporti';

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
// Sego via tutti i records nella tabella SDS_SOCI_IMPIEGHIRACCOLTA
// -------------------------------------------------------------------------------
$truncatetabella_tab_imprac = mysqli_query($connection,"TRUNCATE SDS_SOCI_IMPIEGHIRACCOLTA") 
									or die(mysqli_error($connection));
      
// -------------------------------------------------------------------------------
// e ricreo tutto il set di dati in SDS_SOCI_IMPIEGHIRACCOLTA
// -------------------------------------------------------------------------------
$select_tmp_imprac = "
SELECT
     IMPIEGHI_E_RACCOLTA_01.SOCIO_ISTITUTO,
     IMPIEGHI_E_RACCOLTA_01.NAG  ,
     IMPIEGHI_E_RACCOLTA_01.INTESTAZIONE  ,
     IMPIEGHI_E_RACCOLTA_01.NAG AS NAG_COLL ,
     IMPIEGHI_E_RACCOLTA_01.INTESTAZIONE AS INTESTAZIONE_COLL  ,
     IMPIEGHI_E_RACCOLTA_01.FIL_ANAGRAFICA  ,
     IMPIEGHI_E_RACCOLTA_01.DESC_STATUS  ,
     IMPIEGHI_E_RACCOLTA_01.PRESENZA_RAPPORTI  ,
     IMPIEGHI_E_RACCOLTA_01.TOT_ACCORDATO  ,
     IMPIEGHI_E_RACCOLTA_01.TOT_UTILIZZATO  ,
     IMPIEGHI_E_RACCOLTA_01.TOT_RACCOLTA  ,
     IMPIEGHI_E_RACCOLTA_01.DATA_RIFERIMENTO  ,
     IMPIEGHI_E_RACCOLTA_01.TOT_RACC_DIRETTA ,
     IMPIEGHI_E_RACCOLTA_01.RACC_IND_AMMINISTRATA  ,
     IMPIEGHI_E_RACCOLTA_01.RACC_IND_GESTITA  ,
     IMPIEGHI_E_RACCOLTA_01.TOT_RACC_INDIRETTA,
     IMPIEGHI_E_RACCOLTA_01.N_RAPP_IMPIEGHI  ,
     IMPIEGHI_E_RACCOLTA_01.N_RAPP_RACC_NO_DT  ,
     IMPIEGHI_E_RACCOLTA_01.N_RAPP_RACC_DOSSIER 
FROM
    IMPIEGHI_E_RACCOLTA  AS IMPIEGHI_E_RACCOLTA_01  
WHERE
    IMPIEGHI_E_RACCOLTA_01.SOCIO_ISTITUTO = '1'

UNION   

SELECT
     IMPIEGHI_E_RACCOLTA_01.SOCIO_ISTITUTO AS SOCIO_ISTITUTO,
     ANAG_COLL_NDG_NDG_01.NAG_1,
     IMPIEGHI_E_RACCOLTA_01.INTESTAZIONE AS INTESTAZIONE  ,
     ANAG_COLL_NDG_NDG_01.NAG_2 AS NAG_COLL ,
     IMPIEGHI_E_RACCOLTA_02.INTESTAZIONE AS INTESTAZIONE_COLL ,
     IMPIEGHI_E_RACCOLTA_02.FIL_ANAGRAFICA AS FIL_ANAGRAFICA  ,
     IMPIEGHI_E_RACCOLTA_02.DESC_STATUS AS DESC_STATUS ,
     IMPIEGHI_E_RACCOLTA_02.PRESENZA_RAPPORTI AS PRESENZA_RAPPORTI ,
     IMPIEGHI_E_RACCOLTA_02.TOT_ACCORDATO AS TOT_ACCORDATO ,
     IMPIEGHI_E_RACCOLTA_02.TOT_UTILIZZATO AS TOT_UTILIZZATO ,
     IMPIEGHI_E_RACCOLTA_02.TOT_RACCOLTA AS TOT_RACCOLTA ,
     IMPIEGHI_E_RACCOLTA_02.DATA_RIFERIMENTO AS DATA_RIFERIMENTO ,
     IMPIEGHI_E_RACCOLTA_02.TOT_RACC_DIRETTA AS TOT_RACC_DIRETTA ,
     IMPIEGHI_E_RACCOLTA_02.RACC_IND_AMMINISTRATA AS RACC_IND_AMMINISTRATA ,
     IMPIEGHI_E_RACCOLTA_02.RACC_IND_GESTITA AS RACC_IND_GESTITA ,
     IMPIEGHI_E_RACCOLTA_02.TOT_RACC_INDIRETTA AS TOT_RACC_INDIRETTA ,
     IMPIEGHI_E_RACCOLTA_02.N_RAPP_IMPIEGHI AS N_RAPP_IMPIEGHI ,
     IMPIEGHI_E_RACCOLTA_02.N_RAPP_RACC_NO_DT AS N_RAPP_RACC_NO_DT ,
     IMPIEGHI_E_RACCOLTA_02.N_RAPP_RACC_DOSSIER AS N_RAPP_RACC_DOSSIER 
FROM
    IMPIEGHI_E_RACCOLTA  AS IMPIEGHI_E_RACCOLTA_01 INNER JOIN ANAG_COLL_NDG_NDG AS ANAG_COLL_NDG_NDG_01  
    ON (IMPIEGHI_E_RACCOLTA_01.NAG = ANAG_COLL_NDG_NDG_01.NAG_1 ) ,
    ANAG_COLL_NDG_NDG  AS ANAG_COLL_NDG_NDG_01 INNER JOIN IMPIEGHI_E_RACCOLTA AS IMPIEGHI_E_RACCOLTA_02  
    ON (ANAG_COLL_NDG_NDG_01.NAG_2 = IMPIEGHI_E_RACCOLTA_02.NAG )  
WHERE
    ANAG_COLL_NDG_NDG_01.NUM_COLLEG IN ( '10','16' ) 
AND
    IMPIEGHI_E_RACCOLTA_01.SOCIO_ISTITUTO = '1'    
        ";

$query_imprac = odbc_exec($connect, $select_tmp_imprac);
while($dati_imprac = odbc_fetch_object($query_imprac)){ 

      $select_insert_imprac = "
            INSERT INTO SDS_SOCI_IMPIEGHIRACCOLTA
            VALUES 
           (
             '".$dati_imprac->SOCIO_ISTITUTO."'
            ,'".$dati_imprac->NAG."'
            ,'".mysqli_real_escape_string($connection, $dati_imprac->INTESTAZIONE)."'
            ,'".$dati_imprac->NAG_COLL."'
            ,'".mysqli_real_escape_string($connection, $dati_imprac->INTESTAZIONE_COLL)."'
            ,'".substr($dati_imprac->FIL_ANAGRAFICA,1,3)."'
            ,'".$dati_imprac->DESC_STATUS."'
            ,'".$dati_imprac->PRESENZA_RAPPORTI."'
            ,'".$dati_imprac->TOT_ACCORDATO."'
            ,'".$dati_imprac->TOT_UTILIZZATO."'
            ,'".$dati_imprac->TOT_RACCOLTA."'

            ,'".$dati_imprac->DATA_RIFERIMENTO."'
            ,'".$dati_imprac->TOT_RACC_DIRETTA."'
            ,'".$dati_imprac->RACC_IND_AMMINISTRATA."'
            ,'".$dati_imprac->RACC_IND_GESTITA."'
            ,'".$dati_imprac->TOT_RACC_INDIRETTA."'
            ,'".$dati_imprac->N_RAPP_IMPIEGHI."'
            ,'".$dati_imprac->N_RAPP_RACC_NO_DT."'
            ,'".$dati_imprac->N_RAPP_RACC_DOSSIER."'
            )
            ";

        mysqli_query($connection, $select_insert_imprac )
                    or die("INSERT --- ".mysqli_error($connection));;
      }

// ---- Aggiorno la tabella ULTIMO_CARICAMENTO
$updcaricamento = "     UPDATE tab_ultimo_caricamento
                      SET   caricamento=now(), fonte='SDS_SOCI_IMPIEGHIRACCOLTA' 
                      WHERE fonte='SDS_SOCI_IMPIEGHIRACCOLTA'
                ";
$querydati_updcaricamento = mysqli_query($connection, $updcaricamento);     
		