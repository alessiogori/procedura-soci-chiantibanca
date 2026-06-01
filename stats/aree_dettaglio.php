<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE



echo '<table border="0" width="90%" align="center">
        <tr>
          <td  valign="top">';

$select_tot = " SELECT sum(TOTALE_POSIZIONI) as qta, sum(qta_PF) as PF, sum(qta_PG) as PG 
                FROM  view_pf_pg  " ;

$qry_tot = $dbhandle->query($select_tot) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($tot = mysqli_fetch_array($qry_tot)){ 
  $soci_totale = $tot['qta'];
  $soci_totale_pf = $tot['PF'];
  $soci_totale_pg = $tot['PG'];
}    

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
/*
$strQuery_aree1 = "SELECT count(*) as qta, area 
                  FROM sds_soci as a, tab_psw as p 
                  WHERE a.DATA_ENTRATA <= now()
                  AND ( (a.DATA_USCITA = '0')
                      OR (a.DATA_USCITA > now() ) )
                  AND a.FILIALE_CAPOFILA = CAST(p.filiale AS UNSIGNED)
                  GROUP BY area 
                  ORDER BY 2    ";
*/

$strQuery_aree1 = "SELECT 
                  area, FILIALE_CAPOFILA, desc_filiale, 
                  sum(TOTALE_POSIZIONI) as qta, 
                  sum(qta_PF) as PF, 
                  sum(qta_PG) as PG
                  FROM
                    view_pf_pg
                  GROUP BY area ";             

$result_aree1 = $dbhandle->query($strQuery_aree1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table class="table table-bordered table-hover"  border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Qtà Soci</td>
          <td align="right">PF</td>
          <td align="right">PG</td>
          <td align="right"><small>Perc %<br>Area/Banca</small></td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree1 = mysqli_fetch_array($result_aree1)) {

    $percArea = number_format(($row_aree1['qta'] / $soci_totale )*100,2,',','.');

    echo "<tr>
            <td>".$row_aree1['area']."</td>
            <td align='right'>".$row_aree1['qta']."</td>
            <td align='right'>".$row_aree1['PF']."</td>
            <td align='right'>".$row_aree1['PG']."</td>
            <td align='right'><i>".$percArea." %</i></td>
          </tr>
        ";
  }

echo '  <tr class="table-secondary">
          <td></td>
          <td align="right">'.number_format($soci_totale,0,',','.').'</td>
          <td align="right">'.number_format($soci_totale_pf,0,',','.').'</td>
          <td align="right">'.number_format($soci_totale_pg,0,',','.').'</td>
          <td></td>
        </tr>
</table>';


echo '</td>
<td>&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;</td>
      <td  valign="top">';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE IN DETTAGLIO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
/*
$strQuery_aree2 = "SELECT count(*) as qta, area, FILIALE_CAPOFILA, desc_filiale, 
                  case when
                   tipo_nag = 'PF' then 'PF'
                   else 'PG' end as tipo
                  FROM sds_soci as a, tab_psw as p 
                  WHERE a.DATA_ENTRATA <= now()
                  AND ( (a.DATA_USCITA = '0')
                      OR (a.DATA_USCITA > now() ) )
                  AND a.FILIALE_CAPOFILA = CAST(p.filiale AS UNSIGNED)
                  GROUP BY area, FILIALE_CAPOFILA, desc_filiale, tipo
                  ORDER BY 2,3   ";
*/
$strQuery_aree2 = "SELECT 
                  area, FILIALE_CAPOFILA, desc_filiale, 
                  sum(TOTALE_POSIZIONI) as qta, 
                  sum(qta_PF) as PF, 
                  sum(qta_PG) as PG
                  FROM
                    view_pf_pg
                  GROUP BY cast(FILIALE_CAPOFILA as unsigned) ";                  

$result_aree2 = $dbhandle->query($strQuery_aree2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table class="table table-bordered table-hover" border="0" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="center">CodFil</td>
          <td>Nome Filiale</td>
          <td align="right">Qtà Soci</td>
          <td align="right">PF</td>
          <td align="right">PG</td>
          <td align="right"><small>Perc %<br>Filiale/Banca</small></td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree2 = mysqli_fetch_array($result_aree2)) {

    $percFiliale = number_format(($row_aree2['qta'] / $soci_totale )*100,2,',','.');

    echo "<tr>
            <td>".$row_aree2['area']."</td>
            <td align='center'>".$row_aree2['FILIALE_CAPOFILA']."</td>
            <td>".$row_aree2['desc_filiale']."</td>
            <td align='right'>".number_format($row_aree2['qta'],0,',','.')."</td>
            <td align='right'>".$row_aree2['PF']."</td>
            <td align='right'>".$row_aree2['PG']."</td>            
            <td align='right'><i>".$percFiliale." %</i></td>
        </tr>
        ";
  }

echo '  <tr class="table-secondary">
          <td></td>
          <td></td>
          <td></td>
          <td align="right">'.number_format($soci_totale,0,',','.').'</td>
          <td align="right">'.number_format($soci_totale_pf,0,',','.').'</td>
          <td align="right">'.number_format($soci_totale_pg,0,',','.').'</td>
          <td></td>
        </tr>
</table>';

echo '
</td></tr></table>';
?>