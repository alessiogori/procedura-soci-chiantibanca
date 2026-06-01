<?php
//////////////////////////////////////////////////////////////////
// SITUAZIONE SOCI
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'SITUAZIONE';
$TITOLO = 'Situazione Soci';

// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

echo '
<center>
<div class="col-lg-12">
  <div class="alert alert-dismissible alert-success"><h3>Situazione Soci</h3>
  '.$titolofiliale.'</div>
</div>
';


// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------


// ----------------------------------------------------------------------------
// CAPITALE SOCIALE E NUMERO AZIONI - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO
// ----------------------------------------------------------------------------
$select_Capitale =   "        SELECT 'A1 - Capitale Sociale alla data iniziale' as Tipo, '".$_GET['datain']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND cert.DATA_ACQUISTO <= '".$_GET['datain']."'
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$_GET['datain']."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$_GET['datain']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ".$condizionefiliale5."
                              
                              UNION
                              
                              SELECT 'A2 - Capitale Sociale incremento' as Tipo, '".$_GET['datain']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO >= '".$_GET['datain']."' AND
                                     cert.DATA_ACQUISTO <= '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')
                              ".$condizionefiliale5."
                              
                              UNION

                              SELECT 'A3 - Capitale Sociale decremento' as Tipo, '".$_GET['datain']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND ((cert.DATA_ANNULLAMENTO >= '".$_GET['datain']."' AND
                                       cert.DATA_ANNULLAMENTO <= '".$_GET['dataout']."') OR
                                       (cert.DATA_VENDITA >= '".$_GET['datain']."' AND
                                       cert.DATA_VENDITA <= '".$_GET['dataout']."'))
                              AND mov.ctipomov not in
                                       ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU', 'AN','RS','VE','CA','UC')  
                              ".$condizionefiliale5."
                              
                              UNION

                              SELECT 'A4 - Capitale Sociale alla data finale' as Tipo, '".$_GET['dataout']."' as Periodo, sum(cert.NAZIONI * 30.33) as CapitaleSociale, sum(cert.NAZIONI) as NumeroAzioni
                              FROM SOCI_CERTIFICATI cert, SOCI_movimenti MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                              Where mov.idcertificato = cert.idcertificato
                              AND mov.idsocio = soci.idsocio
                              AND soci.nag = anag.nag
                              AND (cert.DATA_ACQUISTO <= '".$_GET['dataout']."')
                              AND (cert.DATA_ANNULLAMENTO = '00/00/0000' OR
                                     cert.DATA_ANNULLAMENTO > '".$_GET['dataout']."')
                              AND (cert.DATA_VENDITA = '00/00/0000' OR cert.DATA_VENDITA > '".$_GET['dataout']."')
                              AND mov.ctipomov not in
                                     ('QA','QR','AR', 'AQ', 'AS', 'CQ', 'DE', 'ID', 'RQ', 'TR','CO','CU','DO','FU','SU','AN','RS','VE','CA','UC')
                              ".$condizionefiliale5."                                     
                              ";
             //echo $select_Capitale;
// ---------------------------------------------------------
// SOVRAPPREZZO - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO
// ---------------------------------------------------------
$select_Sovrapprezzo    = "   SELECT 'B1 - Sovrapprezzo alla data iniziale' as Tipo, '".$_GET['datain']."' as Periodo, sum(mov.isovrapprezzo) as Valore
                                    FROM SOCI_MOVIMENTI mov, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where mov.DATA_MOVIMENTO <= '".$_GET['datain']."'
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale5."

                              UNION

                              SELECT 'B2 - Sovrapprezzo incremento' as Tipo, '".$_GET['datain']."' as Periodo, SUM(MOV.ISOVRAPPREZZO) as Valore
                                    FROM SOCI_MOVIMENTI MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where MOV.ISOVRAPPREZZO > 0
                                    AND (mov.DATA_MOVIMENTO >= '".$_GET['datain']."' AND
                                           mov.DATA_MOVIMENTO <= '".$_GET['dataout']."')
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale5."
                              
                              UNION

                              SELECT 'B3 - Sovrapprezzo decremento' as Tipo, '".$_GET['datain']."' as Periodo, SUM(MOV.ISOVRAPPREZZO) as Valore
                                    FROM SOCI_MOVIMENTI MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where MOV.ISOVRAPPREZZO < 0
                                    AND (mov.DATA_MOVIMENTO >= '".$_GET['datain']."' AND
                                     mov.DATA_MOVIMENTO <= '".$_GET['dataout']."')
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale5."

                              UNION

                              SELECT 'B4 - Sovrapprezzo alla data finale' as Tipo, '".$_GET['dataout']."' as Periodo, SUM(MOV.ISOVRAPPREZZO) as Valore
                                    FROM SOCI_MOVIMENTI MOV, SOCI_ANAGRAFICA soci, ANAG_NAG anag
                                    Where mov.DATA_MOVIMENTO <= '".$_GET['dataout']."'
                                    AND mov.idsocio = soci.idsocio
                                    AND soci.nag = anag.nag
                                    ".$condizionefiliale5."
                              ";

// ----------------------------------------------------------
// QUANTITA' SOCI - INIZIO/INCREMENTO/DECREMENTO/FINE PERIODO
// ----------------------------------------------------------
$select_Soci =         "      SELECT 'C1 - Soci alla data iniziale' as Tipo, '".$_GET['datain']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_ENTRATA <= '".$_GET['datain']."'
                                    AND (DATA_USCITA = '00/00/0000'
                                          OR DATA_USCITA > '".$_GET['datain']."')
                                    ".$condizionefiliale5."

                              UNION

                              SELECT 'C2 - Soci incrementati' as Tipo, '".$_GET['datain']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_ENTRATA >= '".$_GET['datain']."'
                                    AND DATA_ENTRATA <= '".$_GET['dataout']."'
                                    ".$condizionefiliale5."
                              
                              UNION

                              SELECT 'C3 - Soci decrementati' as Tipo, '".$_GET['datain']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_USCITA >= '".$_GET['datain']."'
                                    AND DATA_USCITA <= '".$_GET['dataout']."'
                                    ".$condizionefiliale5."

                              UNION

                              SELECT 'C4 - Soci alla data finale' as Tipo, '".$_GET['dataout']."' as Periodo, count(*) as qta
                                    FROM SOCI_ANAGRAFICA, ANAG_NAG
                                    WHERE SOCI_ANAGRAFICA.NAG = ANAG_NAG.NAG
                                    AND DATA_ENTRATA <= '".$_GET['dataout']."'
                                    AND (DATA_USCITA = '00/00/0000'
                                          OR DATA_USCITA > '".$_GET['dataout']."')
                                    ".$condizionefiliale5."
                              ";

//echo $select_Soci;
// -----------------------------------------------------------------
// Estrazione dei valori dalle singole Select
// -----------------------------------------------------------------

echo '<table border="0" align="center" width="100%">
        <tr>
            <td valign="top">';


      // CAPITALE SOCIALE
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-success">
                <td align="left"><small>CAPITALE SOCIALE</td>
                <td align="center"><small>Periodo</td>
                <td align="right"><small>Valore Capitale</td>
                <td align="right"><small>Numero Azioni</td>
              </tr>';

      $result_Capitale = odbc_exec($connect, $select_Capitale);
      while($dati_Capitale = odbc_fetch_object($result_Capitale)) {

            if ($dati_Capitale->TIPO == 'A1 - Capitale Sociale alla data iniziale')     {$valore_inizio = $dati_Capitale->CAPITALESOCIALE;      $azioni_inizio = $dati_Capitale->NUMEROAZIONI;  }
            if ($dati_Capitale->TIPO == 'A4 - Capitale Sociale alla data finale')       {$valore_fine   = $dati_Capitale->CAPITALESOCIALE;      $azioni_fine   = $dati_Capitale->NUMEROAZIONI;  }

                echo "<tr style='color:gray;'>
                        <td ><small>".$dati_Capitale->TIPO."</td>
                        <td align='center'><small>".$dati_Capitale->PERIODO."</td>
                        <td align='right'><small>".number_format($dati_Capitale->CAPITALESOCIALE,0,',','.')."</td>
                        <td align='right'><small>".number_format($dati_Capitale->NUMEROAZIONI,0,',','.')."</td>
                      </tr>
                    ";

      }
            if (($valore_fine - $valore_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:lightred;"> &#8722;'; }
            if (($azioni_fine - $azioni_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:red;"> &#8722;'; }

                echo "<tr style='color:gray;'>
                        <td><small></td>
                        <td><small></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($valore_fine - $valore_inizio),0,',','.')."</b></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($azioni_fine - $azioni_inizio),0,',','.')."</b></td>
                      </tr>
                      </table>";      

echo '</td>
      <td>&nbsp;&nbsp;</td>
      <td valign="top">';

      // SOVRAPPREZZO SOCI
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-success">
                <td align="left"><small>SOVRAPPREZZO</td>
                <td align="center"><small>Periodo</td>
                <td align="right"><small>Valore Sovrapprezzo</td>
              </tr>';

      $result_Sovrapprezzo = odbc_exec($connect, $select_Sovrapprezzo);
      while($dati_Sovrapprezzo = odbc_fetch_object($result_Sovrapprezzo)) {

            if ($dati_Sovrapprezzo->TIPO == 'B1 - Sovrapprezzo alla data iniziale')     {$valore_inizio = $dati_Sovrapprezzo->VALORE;}
            if ($dati_Sovrapprezzo->TIPO == 'B4 - Sovrapprezzo alla data finale')       {$valore_fine   = $dati_Sovrapprezzo->VALORE;}

                echo "<tr style='color:gray;'>
                        <td><small>".$dati_Sovrapprezzo->TIPO."</td>
                        <td align='center'><small>".$dati_Sovrapprezzo->PERIODO."</td>
                        <td align='right'><small>".number_format($dati_Sovrapprezzo->VALORE,0,',','.')."</td>
                      </tr>
                    ";

      }
            if (($valore_fine - $valore_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:red;"> &#8722;'; }

                echo "<tr style='color:gray;'>
                        <td><small></td>
                        <td><small></td>
                        <td align='right'><small><b ".$plusminus.' '.number_format(($valore_fine - $valore_inizio),0,',','.')."</b></td>
                      </tr>
                      </table>";        

echo '</td>
      <td>&nbsp;&nbsp;</td>
      <td valign="top">';

      // QUANTITA' SOCI
      // -----------------------------------------------------------------
      echo ' <table class="table table-bordered table-hover" id="dataTable" width="90%" cellspacing="0">
              <tr class="table-success">
                <td align="left"><small>NUMERO DI SOCI</td>
                <td align="center"><small>Periodo</td>
                <td align="right"><small>Quantità</td>
              </tr>';

      $result_Soci = odbc_exec($connect, $select_Soci);
      while($dati_Soci = odbc_fetch_object($result_Soci)) {

            if ($dati_Soci->TIPO == 'C1 - Soci alla data iniziale')     {$soci_inizio = $dati_Soci->QTA;}
            if ($dati_Soci->TIPO == 'C4 - Soci alla data finale')       {$soci_fine   = $dati_Soci->QTA;}

                echo "<tr style='color:gray;'>
                        <td><small>".$dati_Soci->TIPO."</td>
                        <td align='center'><small>".$dati_Soci->PERIODO."</td>
                        <td align='right'><small>".number_format($dati_Soci->QTA,0,',','.')."</td>
                      </tr>
                    ";

      }
            if (($soci_fine - $soci_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:lightred;"> &#8722;'; }

                echo "<tr style='color:gray;'>
                        <td><small></td>
                        <td><small></td>
                        <td align='right'><small><b ".$plusminus.' '.($soci_fine - $soci_inizio)."</b></td>
                      </tr>
                      </table>";        


echo '</td>
      </tr>
      </table>';


// Close ODBC
odbc_close($connect);


// ----------------------------------------------------
// REPORT AMMISSIONI USCITE
// ----------------------------------------------------

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// -------------------------------------------------------------------------------
// CREAZIONE VISTA TEMPORANEA
// -------------------------------------------------------------------------------
$truncateview = mysqli_query($dbhandle,"DROP VIEW TMP_SOCI_INOUT") or die(mysql_error());;
$createview = mysqli_query($dbhandle," 
                    CREATE VIEW TMP_SOCI_INOUT AS
                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, count(*) as 'Soci_inizio',  '' as  'Incremento', '' as 'Decremento', '' as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                        str_to_date(DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('".$_GET['datain']."','%d/%m/%Y')  
                    AND
                        ( DATA_USCITA =  '0'  
                    OR
                        str_to_date(DATA_USCITA,'%d/%m/%Y') >  str_to_date('".$_GET['datain']."','%d/%m/%Y')  )
                    GROUP BY Area, Filiale
                        
                    UNION

                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, '' as 'Soci_inizio',  count(*) as  'Incremento', '' as 'Decremento', '' as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                        str_to_date(DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')  
                    AND
                        DATA_ENTRATA < NOW()
                    GROUP BY Area, Filiale
                        
                    UNION

                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, '' as 'Soci_inizio',  '' as  'Incremento', count(*) as 'Decremento', '' as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                             str_to_date(DATA_USCITA,'%d/%m/%Y') >= str_to_date('".$_GET['datain']."','%d/%m/%Y') AND
                             DATA_USCITA <= NOW()
                    GROUP BY Area, Filiale

                    UNION

                    SELECT '".$_GET['datain']."' as Periodo, Area, soci.FILIALE_CAPOFILA as Filiale, desc_filiale as NomeFiliale, '' as 'Soci_inizio',  '' as  'Incremento', '' as 'Decremento', count(*)  as 'Soci_fine'
                    FROM
                        SDS_SOCI soci, tab_psw area
                    WHERE
                        soci.FILIALE_CAPOFILA = cast(area.filiale as unsigned)
                    AND
                             DATA_ENTRATA <= NOW()
                             AND (DATA_USCITA =  '0' OR
                                     DATA_USCITA > NOW())
                    GROUP BY Area, Filiale
                    ORDER BY 1  
            ") or die(mysql_error());;             


// -------------------------------------------------------------------------------
// MEDIA PER ANNO
// -------------------------------------------------------------------------------
$media = "  SELECT '2020' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2020
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2021' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2021
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            UNION
            SELECT '2022' as AnnoMeseRichiesta, max(substr(AnnoMeseRichiesta,5,2)) as MesiCount,
            round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_entrati, 
            round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) as media_qta_usciti,
            (round(sum(qta_entrati)/max(substr(AnnoMeseRichiesta,5,2))) - 
             round(sum(qta_usciti)/max(substr(AnnoMeseRichiesta,5,2))) ) as Diff
            FROM view_ammissioni_uscite
            WHERE substr(AnnoMeseRichiesta,1,4) = 2022
            GROUP BY substr(AnnoMeseRichiesta,1,4)
            ORDER BY 1 ASC
                ";


$result_media = $dbhandle->query($media) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");


$tab_media = '<table class="table table-bordered table-hover" border="1" width="90%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="5" align="center">MEDIA BANCA</td>
        </tr>
        <tr class="table-secondary">
          <td>Anno</td>
          <td align="right">Qtà Mesi</td>
          <td align="right">Media Entrati</td>
          <td align="right">Media Usciti</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_media = mysqli_fetch_array($result_media)) {

    if (number_format($row_media['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:green;"';}

    $tab_media .= "<tr style='color:gray;'>
            <td>".$row_media['AnnoMeseRichiesta']."</td>
            <td align='right'>".number_format($row_media['MesiCount'],0,',','.')."</td>
            <td align='right'>".number_format($row_media['media_qta_entrati'],0,',','.')."</td>
            <td align='right'>".number_format($row_media['media_qta_usciti'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_media['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_media .= '</table>';

// -------------------------------------------------------------------------------
// DETTAGLIO AREE
// -------------------------------------------------------------------------------
$dett_aree = "  SELECT Area, 
                round(sum(Soci_inizio)) as SociInizio, 
                round(sum(Incremento))  as Incremento,
                round(sum(Decremento))  as Decremento,
                round(sum(Soci_fine))  as SociFine,
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) as Diff
                FROM tmp_soci_inout
                WHERE Periodo >= '".$_GET['datain']."'
                ".$condizionefiliale."
                GROUP BY Area WITH ROLLUP
                ";

$result_dett_aree = $dbhandle->query($dett_aree) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_aree = '<table class="table table-bordered table-hover" border="1" width="90%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="6" align="center">SITUAZIONE AREA</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Soci iniziali</td>
          <td align="right">Incremento</td>
          <td align="right">Decremento</td>
          <td align="right">Soci finali</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_aree = mysqli_fetch_array($result_dett_aree)) {

    if ($row_dett_aree['Diff'] < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:green;"';}

    $tab_dett_aree .= "<tr style='color:gray;'>
            <td>".$row_dett_aree['Area']."</td>
            <td align='right'>".number_format($row_dett_aree['SociInizio'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['Incremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['Decremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['SociFine'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_aree['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_aree .= '</table>';     

// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI
// -------------------------------------------------------------------------------
$dett_fil = "   SELECT Area, Filiale, NomeFiliale, 
                round(sum(Soci_inizio)) as SociInizio, 
                round(sum(Incremento))  as Incremento,
                round(sum(Decremento))  as Decremento,
                round(sum(Soci_fine))  as SociFine,
                (round(sum(Soci_fine)) - 
                 round(sum(Soci_inizio))) as Diff
                FROM tmp_soci_inout
                WHERE Periodo >= '".$_GET['datain']."'
                ".$condizionefiliale."
                GROUP BY Area, Filiale, NomeFiliale  
                ";

$result_dett_fil = $dbhandle->query($dett_fil) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_fil = '<table  class="table table-bordered table-hover" border="1" width="90%" valign="top" align="center">
        <tr class="table-primary">
          <td colspan="8" align="center">SITUAZIONE FILIALE</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td>Filiale</td>
          <td>Nome Filiale</td>
          <td align="right">Soci iniziali</td>
          <td align="right">Incremento</td>
          <td align="right">Decremento</td>
          <td align="right">Soci finali</td>
          <td align="right">&#177;</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_fil = mysqli_fetch_array($result_dett_fil)) {

    if (number_format($row_dett_fil['Diff'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:green;"';}

    $tab_dett_fil .= "<tr style='color:gray;'>
            <td>".$row_dett_fil['Area']."</td>
            <td>".$row_dett_fil['Filiale']."</td>
            <td>".$row_dett_fil['NomeFiliale']."</td>
            <td align='right'>".number_format($row_dett_fil['SociInizio'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['Incremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['Decremento'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['SociFine'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_fil['Diff'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_fil .= '</table>';     


// -------------------------------------------------------------------------------
// COSTRUZIONE LAYOUT
// -------------------------------------------------------------------------------

echo '
<table border="0" align="center" width="100%">
';

if ($rif == 'Filiale') 
{
  echo '       
       <tr><td></td></tr>
  '; 
}
elseif ( ($rif == 'Area') OR ($rif == '') )
{
  echo '       
  <tr>     
       <td valign="top" width="29%">'.$tab_media.'</td>
       <td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td valign="top" width="69%">'.$tab_dett_aree.'</td>
  </tr>
  ';     
}

echo '  
  <tr>     
       <td valign="top" colspan="3" width="100%"><br>'.$tab_dett_fil.'</td>
  </tr>
</table>
<br>
';
//<!-- <center><h4>Aggiungere link per elenco</h4></center> -->

// closing database connection      
$dbhandle->close();             
?>



