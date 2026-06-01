<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

echo '<table border="0" width="90%" align="center">
        <tr>
          <td  valign="top">';


// CREO LA VISTA DI APPOGGIO
$truncateviewA = mysqli_query($dbhandle,"DROP VIEW view_ammissioni2") or die(mysql_error());;
$viewA = mysqli_query($dbhandle," CREATE VIEW view_ammissioni2 as 
                  SELECT 
                  CONCAT(substring(a.CDA,7,4), substring(a.CDA,4,2))  as AnnoMeseRichiesta,
                  Flag_da_SUCC_CESS, count(*) as qta, area, a.Filiale, desc_filiale
                  FROM tab_xls_ammissioni as a, tab_psw as p 
                  WHERE a.Filiale = CAST(p.filiale AS UNSIGNED)
                  GROUP BY Flag_da_SUCC_CESS, area, a.Filiale, desc_filiale
                  ORDER BY 2,3 
            ") or die(mysql_error());;
            
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Aree per quantità di ammissioni
       </div>';
$strQuery_aree1 = ' SELECT area, 
                    SUM(qta) as Totale,
                    MAX(CASE WHEN Flag_da_SUCC_CESS = "C" THEN qta END ) "Cessioni",
                    MAX(CASE WHEN Flag_da_SUCC_CESS = "S" THEN qta END ) "Successioni",
                    MAX(CASE WHEN Flag_da_SUCC_CESS = "D" THEN qta END ) "Donazioni"
                    FROM view_ammissioni2
                    GROUP BY area
                    ORDER BY 1   ';

$result_aree1 = $dbhandle->query($strQuery_aree1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table border="1" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Qtà<br>Ammissioni</td>
          <td align="right">di cui da<br>Cessioni</td>
          <td align="right">di cui da<br>Successioni</td>
          <td align="right">di cui da<br>Donazioni</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree1 = mysqli_fetch_array($result_aree1)) {

    echo "<tr>
            <td>".$row_aree1['area']."</td>
            <td align='right'>".number_format($row_aree1['Totale'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['Cessioni'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['Successioni'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree1['Donazioni'],0,',','.')."</td>
          </tr>
        ";
  }

echo '</table>';

echo '</td>
<td style="background-color:gray;"></td>
<td>&nbsp;</td>
      <td  valign="top">';

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE IN DETTAGLIO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------

echo ' <div class="alert alert-dismissible alert-primary">
        <strong>Dettaglio Aree/Filiali per quantità di ammissioni
       </div>';

$strQuery_aree2 = ' SELECT area, Filiale, desc_filiale,
                    SUM(qta) as Totale,
                    MAX(CASE WHEN Flag_da_SUCC_CESS = "C" THEN qta END ) "Cessioni",
                    MAX(CASE WHEN Flag_da_SUCC_CESS = "S" THEN qta END ) "Successioni",
                    MAX(CASE WHEN Flag_da_SUCC_CESS = "D" THEN qta END ) "Donazioni"
                    FROM view_ammissioni2
                    GROUP BY area, Filiale, desc_filiale
                    ORDER BY 1   ';

$result_aree2 = $dbhandle->query($strQuery_aree2) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table border="1" width="90%" valign="top">
        <tr class="table-secondary">
          <td>Area</td>
          <td align="center">CodFil</td>
          <td>Nome Filiale</td>
          <td align="right">Qtà<br>Ammissioni</td>
          <td align="right">di cui da<br>Cessioni</td>
          <td align="right">di cui da<br>Successioni</td>
          <td align="right">di cui da<br>Donazioni</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree2 = mysqli_fetch_array($result_aree2)) {

    echo "<tr>
            <td>".$row_aree2['area']."</td>
            <td align='center'>".$row_aree2['Filiale']."&nbsp;</td>
            <td>".$row_aree2['desc_filiale']."</td>
            <td align='right'>".number_format($row_aree2['Totale'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree2['Cessioni'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree2['Successioni'],0,',','.')."</td>
            <td align='right'>".number_format($row_aree2['Donazioni'],0,',','.')."</td>
          </tr>
        ";
  }

echo '</table>

</td></tr></table>';

echo '<div style ="page-break-before: always;"></div>';   // Forzo salto pagina in stampa
 

echo'
</td></tr></table>';
?>