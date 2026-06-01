<?php
//////////////////////////////////////////////////////////////////
// SITUAZIONE SOCI
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
$NOME_SCRIPT = 'PREVISIONALE';
$TITOLO = 'Previsionale Soci';

// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

echo '
<center>
<div class="col-lg-12">
  <div class="alert alert-dismissible alert-success"><h3>Previsionale Soci</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
// FINE SEZIONE DA NON MODIFICARE
// --------------------------------------------------------------------


// -------------------------------------------------------------------------------
// DETTAGLIO AREE
// -------------------------------------------------------------------------------
$dett_aree = "  SELECT
                Area,
                sum(ESCLUSIONE) as ESCLUSIONE,
                sum(ESCLUSIONE_SOFFERENZA) as ESCLUSIONE_SOFFERENZA,
                sum(RECESSO) as RECESSO,
                sum(MORTE) as MORTE,
                sum(CESSIONE_BANCA) as CESSIONE_BANCA,
                sum(TOTALE) as TOTALE,
                sum(NUMERO_SOCI) as NUMERO_SOCI
                FROM tab_previsionale 
                ".$condizionefiliale3."
                GROUP BY Area with ROLLUP
                ";
// echo $dett_aree;
$result_dett_aree = $dbhandle->query($dett_aree) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_aree = '<table class="table" border="1" width="80%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="8" align="center">SITUAZIONE AREA</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td align="right">Esclusione</td>
          <td align="right">Esclusione Soff</td>
          <td align="right">Recesso</td>
          <td align="right">Morte</td>
          <td align="right">Cessione Banca</td>
          <td align="right">Totale</td>
          <td align="right">Ulteriori Soci</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_aree = mysqli_fetch_array($result_dett_aree)) {

    if ($row_dett_aree['NUMERO_SOCI'] < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:red;"';}

    $tab_dett_aree .= "<tr  style='color:black;'>
            <td>".$row_dett_aree['Area']."</td>
            <td align='right'>".number_format($row_dett_aree['ESCLUSIONE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['ESCLUSIONE_SOFFERENZA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['RECESSO'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['MORTE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['CESSIONE_BANCA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_aree['TOTALE'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_aree['NUMERO_SOCI'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_aree .= '</table>';  

// -------------------------------------------------------------------------------
// DETTAGLIO FILIALI
// -------------------------------------------------------------------------------
$dett_fil = "   
            SELECT
            Area,
            Filiale, 
            NomeFiliale,
            sum(ESCLUSIONE) as ESCLUSIONE,
            sum(ESCLUSIONE_SOFFERENZA) as ESCLUSIONE_SOFFERENZA,
            sum(RECESSO) as RECESSO,
            sum(MORTE) as MORTE,
            sum(CESSIONE_BANCA) as CESSIONE_BANCA,
            sum(TOTALE) as TOTALE,
            sum(NUMERO_SOCI) as NUMERO_SOCI
            FROM tab_previsionale 
            ".$condizionefiliale3."
            GROUP BY Filiale
            ORDER BY cast(Filiale as unsigned)
            ";
//echo $dett_fil;
$result_dett_fil = $dbhandle->query($dett_fil) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dett_fil = '<table class="table" border="1" width="80%" valign="top" align="center">
        <tr class="table-warning">
          <td colspan="10" align="center">SITUAZIONE FILIALE</td>
        </tr>
        <tr class="table-secondary">
          <td>Area</td>
          <td>Filiale</td>
          <td>Nome Filiale</td>
          <td align="right">Esclusione</td>
          <td align="right">Esclusione Soff</td>
          <td align="right">Recesso</td>
          <td align="right">Morte</td>
          <td align="right">Cessione Banca</td>
          <td align="right">Totale</td>
          <td align="right">Ulteriori Soci</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_dett_fil = mysqli_fetch_array($result_dett_fil)) {

    if (number_format($row_dett_fil['NUMERO_SOCI'],0,',','.') < 0 ) {$colore = ' style="color:red;"' ;} else {$colore = ' style="color:red;"';}

    $tab_dett_fil .= "<tr style='color:black;'>
            <td>".$row_dett_fil['Area']."</td>
            <td>".$row_dett_fil['Filiale']."</td>
            <td>".$row_dett_fil['NomeFiliale']."</td>
            <td align='right'>".number_format($row_dett_fil['ESCLUSIONE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['ESCLUSIONE_SOFFERENZA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['RECESSO'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['MORTE'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['CESSIONE_BANCA'],0,',','.')."</td>
            <td align='right'>".number_format($row_dett_fil['TOTALE'],0,',','.')."</td>
            <td align='right' ".$colore.">".number_format($row_dett_fil['NUMERO_SOCI'],0,',','.')."</td>
          </tr>
        ";
  }

$tab_dett_fil .= '</table>';     


// -------------------------------------------------------------------------------
// COSTRUZIONE LAYOUT
// -------------------------------------------------------------------------------

echo '
<table border="0" align="center" width="90%">
';

/*
if ($rif != 'filiale') { echo '  <tr><td colspan="2"><div id="amm2"><!-- Fusion Charts will also be rendered here--></div></td></tr>';} 

echo '       
  <tr>     
       <td colspan="2"><div id="amm3"><!-- Fusion Charts will also be rendered here--></div></td>
  </tr>';
*/

if ($rif != 'Filiale') { echo '  
  <tr>     
       <td><br>'.$tab_dett_aree.'</td>
  </tr>
  '; }

echo '  
  <tr>     
       <td><br>'.$tab_dett_fil.'</td>
  </tr>
</table>
<br>
';

?>
