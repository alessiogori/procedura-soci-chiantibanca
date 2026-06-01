<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

echo '
<style type="text/css">
    @import "../css/fontawesome-free/css/all.min.css";
</style>';

echo '<div style ="page-break-before: always;"></div>';   // Forzo salto pagina in stampa

echo '<table border="0" width="90%" align="center">
        <tr>
          <td  valign="top">';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE 1 - Aree per quantità di cessioni in essere
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Cessioni in essere per Aree  
       </div>';
/*
$strQuery_aree1 = "SELECT count(*) as qta, sum(Valore_Nominale) as Valore_Nominale, area
                  FROM tab_xls_cessioni as a, tab_psw as p 
                  WHERE Cessione_a_banca = 'S'
                  -- AND Totale_Parziale = 'T'
                  AND Note_AO08 not in ('S5','S4','SA','SB','SC','SM','VB')
                  AND a.Filiale = CAST(p.filiale AS UNSIGNED)
                  GROUP BY area
                  ORDER BY 2 desc   ";
*/

$strQuery_aree1_tot = " SELECT 
                        sum(QtaCessioni) as qta, sum(Valore_Nominale_Cessioni) as Valore_Nominale,
                        sum(QtaParziali) as qtaP, sum(Valore_Nominale_Cessioni_Parziali) as Valore_Nominale_Parziali,
                        sum(QtaTotali) as qtaT, sum(Valore_Nominale_Cessioni_Totali) as Valore_Nominale_Totali
                        FROM view_cessioni " ;
$qry_aree1_tot = $dbhandle->query($strQuery_aree1_tot) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($aree1tot = mysqli_fetch_array($qry_aree1_tot)){ 
  $qta_totale = $aree1tot['qta'];
  $qta_P = $aree1tot['qtaP'];
  $qta_T = $aree1tot['qtaT'];
  $val_totale = $aree1tot['Valore_Nominale'];
  $val_P = $aree1tot['Valore_Nominale_Parziali'];
  $val_T = $aree1tot['Valore_Nominale_Totali'];
}       

$strQuery_aree1 = "SELECT sum(QtaCessioni) as qta, sum(Valore_Nominale_Cessioni) as Valore_Nominale, 
                        sum(QtaParziali) as qtaP, sum(Valore_Nominale_Cessioni_Parziali) as Valore_Nominale_Parziali,
                        sum(QtaTotali) as qtaT, sum(Valore_Nominale_Cessioni_Totali) as Valore_Nominale_Totali,
                        area
                  FROM view_cessioni 
                  WHERE Area <> '_Chiusa_'
                  GROUP BY area
                  ORDER BY 2 desc ";

$result_aree1 = $dbhandle->query($strQuery_aree1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table class="table table-hover" border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Qtà Cessioni</td>
          <td align="right">Valore Nominale</td>
          <td align="right">Qtà Parz</td>
          <td align="right">VN Parz</td>
          <td align="right">Qtà Tot</td>
          <td align="right">VN Tot</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree1 = mysqli_fetch_array($result_aree1)) {

    echo "<tr>
            <td>".$row_aree1['area']."</td>
            <td align='right'>".number_format($row_aree1['qta'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['Valore_Nominale'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['qtaP'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['Valore_Nominale_Parziali'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['qtaT'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['Valore_Nominale_Totali'],0,',','.')."</td>
          </tr>
        ";
  }

echo '  <tr class="table-secondary">
          <td></td>
          <td align="right">'.number_format($qta_totale,0,',','.').'</td>
          <td align="right">'.number_format($val_totale,0,',','.').'</td>
          <td align="right">'.number_format($qta_P,0,',','.').'</td>
          <td align="right">'.number_format($val_P,0,',','.').'</td>
          <td align="right">'.number_format($qta_T,0,',','.').'</td>
          <td align="right">'.number_format($val_T,0,',','.').'</td>
        </tr>
</table><br><br>';


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// Fasce - Cessioni in essere suddivise per fasce di importi
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Cessioni in essere per fasce di importi
       </div>';
       
$select_fs = " SELECT 'Valore da zero a € 1.000' as Fascia, count(*) as qta, sum(Valore_Nominale) as valore, 
                    sum(Valore_Nominale)/count(*) as media
                FROM
                tab_xls_cessionibanca
                WHERE
                Rimborsato <> 'S'
                AND Valore_Nominale <= 1000

                UNION

                SELECT 'Valore da € 1.001 a € 1.500' as Fascia, count(*) as qta, sum(Valore_Nominale) as valore, 
                    sum(Valore_Nominale)/count(*) as media
                FROM
                tab_xls_cessionibanca
                WHERE
                Rimborsato <> 'S'
                AND Valore_Nominale between 1001 and 1500 

                UNION

                SELECT 'Valore da € 1.501 a € 5.000' as Fascia, count(*) as qta, sum(Valore_Nominale) as valore, 
                    sum(Valore_Nominale)/count(*) as media
                FROM
                tab_xls_cessionibanca
                WHERE
                Rimborsato <> 'S'
                AND Valore_Nominale between 1501 and 5000 

                UNION

                SELECT 'Valore da € 5.501 a € 10.000' as Fascia, count(*) as qta, sum(Valore_Nominale) as valore, 
                    sum(Valore_Nominale)/count(*) as media
                FROM
                tab_xls_cessionibanca
                WHERE
                Rimborsato <> 'S'
                AND Valore_Nominale between 5001 and 10000 

                UNION

                SELECT 'Valore da € 10.001 a € 20.000' as Fascia, count(*) as qta, sum(Valore_Nominale) as valore, 
                    sum(Valore_Nominale)/count(*) as media
                FROM
                tab_xls_cessionibanca
                WHERE
                Rimborsato <> 'S'
                AND Valore_Nominale between 10001 and 20000 

                UNION

                SELECT 'Valore oltre € 20.001' as Fascia, count(*) as qta, sum(Valore_Nominale) as valore, 
                    sum(Valore_Nominale)/count(*) as media
                FROM
                tab_xls_cessionibanca
                WHERE
                Rimborsato <> 'S'
                AND Valore_Nominale >= 20001 " ;

$qry_fs = $dbhandle->query($select_fs) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");


echo '<table class="table table-hover" border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td align="left">Fascia</td>
          <td align="right">Qtà Cessioni</td>
          <td align="right">Totale Nominale</td>
          <td align="right">Valore Medio</td>
        </tr>';

while($fs = mysqli_fetch_array($qry_fs)){ 

    echo "<tr>
            <td>".$fs['Fascia']."</td>
            <td align='right'>".number_format($fs['qta'],0,',','.')."</td>
            <td align='right'>".number_format($fs['valore'],0,',','.')."</td> 
            <td align='right'>".number_format($fs['media'],0,',','.')."</td> 
         </tr>
        ";
  }

echo '</table><br><br>';


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// Riepilogo Statistico
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Sintesi storico Cessioni (ricevute dal 06.2017)
       </div>';
       
$select_stat = "SELECT Anno, 
                sum(Presentate) as Presentate, 
                sum(Pagate) as Pagate, 
                (sum(Presentate) - sum(Pagate)) as Residuo,
                sum(RimborsoTotale) as RimborsoTotale, 
                sum(RimborsoParziale) as RimborsoParziale
                FROM view_cessioni_totali
                GROUP BY Anno WITH rollup";


$qry_stat = $dbhandle->query($select_stat) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");


echo '<table class="table table-hover" border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td align="left">Anno</td>
          <td align="right">Presentate</td>
          <td align="right">Pagate</td>
          <td align="right">Residuo</td>
          <td align="right">di cui Totali</td>
          <td align="right">%</td>
          <td align="right">di cui Parziali</td>
          <td align="right">%</td>
        </tr>';

while($stat = mysqli_fetch_array($qry_stat)){ 

    $PercRimborsoTotale    = ($stat['RimborsoTotale'] / $stat['Presentate']) * 100;
    $PercRimborsoParziale  = ($stat['RimborsoParziale'] / $stat['Presentate']) * 100;

    if ($stat['Anno'] == '') {$coloreriga = ' class="table-secondary"';} else {$coloreriga = '';}

    echo "<tr ".$coloreriga.">
            <td>".$stat['Anno']."</td>
            <td align='right'>".number_format($stat['Presentate'],0,',','.')."</td>
            <td align='right'>".number_format($stat['Pagate'],0,',','.')."</td> 
            <td align='right'>".number_format($stat['Residuo'],0,',','.')."</td> 
            <td align='right'>".number_format($stat['RimborsoTotale'],0,',','.')."</td>
            <td align='right'>".number_format($PercRimborsoTotale,2,',','.')."%</td> 
            <td align='right'>".number_format($stat['RimborsoParziale'],0,',','.')."</td> 
            <td align='right'>".number_format($PercRimborsoParziale,2,',','.')."%</td> 
         </tr>
        ";
  }

echo '</table>';

$select_first_date = "  select min(str_to_date(data_richiesta,'%d/%m/%Y')) as minData
                        from tab_xls_cessionibanca
                        where Rimborsato <> 'S' ";
$qry_first_date = $dbhandle->query($select_first_date) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_first_date = mysqli_fetch_array($qry_first_date)){ 
    $first_date = $dati_first_date['minData'];
}

$date_first = new DateTime($first_date);
echo 'Data prossimo rimborso da effettuare: '.$date_first->format('d.m.Y');              

echo '</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td style="background-color:gray;"></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td  valign="top">';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE 2 - Cessioni in essere in rapporto a qtà Soci su Aree e Banca
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Cessioni in essere in rapporto a qtà Soci su Aree e Banca
       </div>';
       
$select_tot = " SELECT count(*) as qta FROM sds_soci 
                WHERE socio_istituto = 1 " ;
$qry_tot = $dbhandle->query($select_tot) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($tot = mysqli_fetch_array($qry_tot)){ 
  $soci_totale = $tot['qta'];
}       
       
$strQuery_aree2 = " SELECT Area, sum(QtaCessioni) as QtaCessioni, sum(Valore_Nominale_Cessioni) as Valore_Nominale_Cessioni, sum(QtaSoci) as QtaSoci, sum(Valore_Nominale) as Valore_Nominale
                    FROM view_cessioni 
                    WHERE Area <> '_Chiusa_'
                    GROUP BY Area 
                    ORDER BY 2 desc  ";

$result_aree2 = $dbhandle->query($strQuery_aree2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table class="table table-hover" border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Qtà Cessioni</td>
          <td align="right">Qtà Soci</td>
          <td align="right">% su Soci Area</td>
          <td align="right">% su Soci Banca</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree2 = mysqli_fetch_array($result_aree2)) {

    $percArea = number_format(($row_aree2['QtaCessioni'] / $row_aree2['QtaSoci'])*100,2,',','.');
    $percBanca = number_format(($row_aree2['QtaCessioni'] / $soci_totale)*100,2,',','.');
        
    echo "<tr>
            <td>".$row_aree2['Area']."</td>
            <td align='right'>".number_format($row_aree2['QtaCessioni'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree2['QtaSoci'],0,',','.')."</td> 
            <td align='right'>".$percArea." %</td>            
            <td align='right'>".$percBanca." %</td>            
         </tr>
        ";
  }

    $percBanca_tot = number_format(($qta_totale / $soci_totale)*100,2,',','.');


echo '  <tr class="table-secondary">
          <td></td>
          <td align="right">'.number_format($qta_totale,0,',','.').'</td>
          <td align="right">'.number_format($soci_totale,0,',','.').'</td>
          <td></td>
          <td align="right">'.$percBanca_tot.' %</td>
        </tr>
</table><br><br>';


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE CON DETTAGLIO FILIALI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Dettaglio Aree/Filiali per quantità di cessioni in essere
       </div>';

$strQuery_aree2_tot = " SELECT a.area, a.filiale, desc_filiale,
                        sum(QtaCessioni) as qta, 
                        sum(QtaParziali) as qtaP, 
                        sum(QtaTotali) as qtaT,
                        sum(Valore_Nominale_Cessioni) as Valore_Nominale_Cessioni
                        FROM view_cessioni as a, tab_psw as p
                        WHERE a.Filiale = CAST(p.filiale AS UNSIGNED)
                        GROUP BY a.area, a.Filiale, desc_filiale " ;

$qry_aree2_tot = $dbhandle->query($strQuery_aree2_tot) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table class="table table-hover" border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">CodFil</td>
          <td>Nome Filiale</td>
          <td align="right">Qtà Cessioni</td>
          <td align="right">di cui Parziali</td>
          <td align="right">di cui Totali</td>
          <td align="right">Valore Nominale</td>
        </tr>';

while($aree2tot = mysqli_fetch_array($qry_aree2_tot)){ 

    echo "<tr>
            <td>".$aree2tot['area']."</td>
            <td align='right'>".$aree2tot['filiale']."&nbsp;</td>
            <td>".$aree2tot['desc_filiale']."</td>
            <td align='right'>".number_format($aree2tot['qta'],0,',','.')."</td>
            <td align='right'>".number_format($aree2tot['qtaP'],0,',','.')."</td>
            <td align='right'>".number_format($aree2tot['qtaT'],0,',','.')."</td>
            <td align='right'>".number_format($aree2tot['Valore_Nominale_Cessioni'],0,',','.')."</td>
          </tr>
        ";
}
    echo '</table><br><br>';


/*
$strQuery_aree2 = "SELECT count(*) as qta, area, a.Filiale, desc_filiale
                  FROM tab_xls_cessionibanca as a, tab_psw as p 
                  WHERE Rimborsato <> 'S'
                  AND a.Filiale = CAST(p.filiale AS UNSIGNED)
                  GROUP BY area, a.Filiale, desc_filiale
                  ORDER BY 2,3   ";

$result_aree2 = $dbhandle->query($strQuery_aree2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table border="1" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">CodFil</td>
          <td>Nome Filiale</td>
          <td align="right">Qtà Cessioni</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree2 = mysqli_fetch_array($result_aree2)) {

    echo "<tr>
            <td>".$row_aree2['area']."</td>
            <td align='right'>".$row_aree2['Filiale']."&nbsp;</td>
            <td>".$row_aree2['desc_filiale']."</td>
            <td align='right'>".number_format($row_aree2['qta'],0,',','.')."</td>
          </tr>
        ";
  }
*/
echo '</table>

</td></tr></table>';

// -------------------------------------------------------------------------------
//  INDICI
// -------------------------------------------------------------------------------
$select_capitale = " SELECT capitale FROM tab_valorefondo " ;
$qry_capitale = $dbhandle->query($select_capitale) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($capitale = mysqli_fetch_array($qry_capitale)){ 
  $val_capitale = $capitale['capitale'];
}    

echo '<center>
      <h5><i class="fas fa-chart-line fa-1x text-gray-300 col-auto"></i>
      Nr.Cessioni a Banca : Nr.Soci = '.$percBanca_tot.' %</h5>';

$percCapSoc = number_format(($val_totale / $val_capitale)*100,2,',','.');

echo '<h5><i class="fas fa-chart-line fa-1x text-gray-300 col-auto"></i>
      Valore Cessioni a Banca : Capitale Sociale = '.$percCapSoc.' %</h5>';

echo '</center>';

echo '<div style ="page-break-before: always;"></div>';   // Forzo salto pagina in stampa
 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// POSIZIONE oltre EUR 10.000 DI RIMBORSO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo '<br><br>
      <div class="alert alert-dismissible alert-primary">
        <strong>Prime 20 posizioni in essere in ordine di importo
      </div>';

echo '<table border="0" width="90%" align="center">
        <tr>
          <td  valign="top">';


$strQuery_aree3 = "SELECT area, a.Filiale, desc_filiale, a.NAG, Nominativo, 
                  CASE WHEN TIPO_DATO_CNT = 'CELL' THEN VALORE_DATO_CNT END AS Telefono,
                  s.STATO_NAG, Numero_Azioni, 
                  a.Valore_Nominale, Data_Richiesta, Totale_Parziale, Note_Motivazioni,
                  DESC_STATUS as STATUS, 
                  TOT_RACCOLTA, TOT_ACCORDATO, TOT_UTILIZZATO     
                  FROM tab_xls_cessionibanca as a, tab_psw as p,  sds_soci as s, sds_soci_daticontatto as c, view_impieghiraccolta as v
                  WHERE Rimborsato <> 'S'
                  AND a.Valore_Nominale > 5000
                  AND a.Filiale = CAST(p.filiale AS UNSIGNED)
                  AND a.NAG = s.NAG
                  AND a.NAG = c.NAG
                  AND a.NAG = v.NAG 
                  GROUP BY area, a.Filiale, desc_filiale, a.NAG, Nominativo
                  ORDER BY 8 desc,5  
                  LIMIT 20 ";

$result_aree3 = $dbhandle->query($strQuery_aree3) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table class="table table-bordered table-hover" border="0" width="100%" valign="top" style="font-size:14px;">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">CodFil</td>
          <td>Nome Filiale</td>
          <td align="center">CAG</td>
          <td>Nominativo</td>
          <td align="right">Stato</td>
          <td align="right">Telefono</td>
          <td align="right">Numero<br>Azioni</td>
          <td align="right">Valore<br>Nominale</td>
          <td align="center">Data<br>Richiesta</td>
          <td align="center">Totale<br>Parziale</td>
          <td>Note</td>
          <td>Status</td>
          <td align="right">Totale<br>Raccolta</td>
          <td align="right">Totale<br>Accordato</td>
          <td align="right">Totale<br>Utilizzato</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree3 = mysqli_fetch_array($result_aree3)) {

    // decodifica STATO_NAG 
    if ($row_aree3['STATO_NAG'] == '0')  { $statonag = 'Cliente Potenziale'; } 
        elseif ($row_aree3['STATO_NAG'] == '1') { $statonag = 'Cliente con rapporti'; } 
        else   { $statonag = 'Ex Cliente'; } 

    echo "<tr>
            <td>".$row_aree3['area']."</td>
            <td align='right'>".$row_aree3['Filiale']."&nbsp;</td>
            <td>".$row_aree3['desc_filiale']."</td>
            <td align='right'>".$row_aree3['NAG']."&nbsp;</td>
            <td>".$row_aree3['Nominativo']."</td>
            <td align='right'>".$statonag."&nbsp;</td>
            <td align='right'>".$row_aree3['Telefono']."&nbsp;</td>
            <td align='right'>".number_format($row_aree3['Numero_Azioni'],0,',','.')."</td>
            <td align='right'>".floatval($row_aree3['Valore_Nominale'])."</td>
            <td align='center'>".$row_aree3['Data_Richiesta']."</td>
            <td align='center'>".$row_aree3['Totale_Parziale']."</td>
            <td>".$row_aree3['Note_Motivazioni']."</td>
            <td>".$row_aree3['STATUS']."</td>
            <td align='right'>".number_format($row_aree3['TOT_RACCOLTA'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree3['TOT_ACCORDATO'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree3['TOT_UTILIZZATO'],0,',','.')."</td>
          </tr>
        ";
  }

echo '</table>

</td></tr></table>';
?>