<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("../config/_config.php");
include("../config/_functions.php");

// including FusionCharts PHP wrapper
include("../graph/fusioncharts.php"); 

echo '<html>
        <head>
        <script type="text/javascript" src="../js/fusioncharts/fusioncharts.js"></script>
        <script type="text/javascript" src="../js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
        <title>Rilascio Buoni</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';

// FINE SEZIONE DA NON MODIFICARE
// 
// echo '<div style ="page-break-before: always;"></div>';   // Forzo salto pagina in stampa
$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// BUONI RILASCIATI 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
echo ' <div class="alert alert-dismissible alert-success">
        <strong>Assemblea - Buoni consegnati
       </div>';

// Totale       
$strQuery_buoniTot = "  SELECT sum(NumeroBuono) as qta
                        FROM tab_soci_buoni";

$result_buoniTot = $dbhandle->query($strQuery_buoniTot) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while ($row_buoniTot = mysqli_fetch_array($result_buoniTot)) {
    $BuoniTotali = $row_buoniTot['qta'];
}

// Dettaglio
$strQuery_buoniDett ="  SELECT sum(NumeroBuono) as qta, Genere, Filiale, NomeFiliale
                        FROM tab_soci_buoni
                        GROUP BY Genere, Filiale, NomeFiliale
                        ORDER BY 2   ";

$result_buoniDett = $dbhandle->query($strQuery_buoniDett) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '<table border="0" align="center" width="50%" valign="top">
        <tr class="table-secondary">
          <td>Genere</td>
          <td>Filiale</td>
          <td align="right">Qtà Buoni</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_buoniDett = mysqli_fetch_array($result_buoniDett)) {

    echo "<tr>
            <td>".$row_buoniDett['Genere']."</td>
            <td>".$row_buoniDett['Filiale']." ".$row_buoniDett['NomeFiliale']."</td>
            <td align='right'>".number_format($row_buoniDett['qta'],0,',','.')."</td>
          </tr>
        ";
  }

echo '<tr class="alert-light"><td colspan="2">TOTALE</td><td align="right">'.$BuoniTotali.'</td></tr>';

echo '</table>';

						
    // Preparo il file per l'estrazione in CSV
    $contenutofile = '';
    $select_file = "SELECT *
					FROM tab_soci_buoni
					ORDER BY Filiale, Socio
                    " ;
    //echo $select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/rilasciobuoni.csv", "w");
    $contenutofile .= "Socio;CAG;Filiale;NomeFiliale;Genere;NumeroBuono;DataConsegna;IpAddress\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
			$cnt_file['Socio'].";".
			$cnt_file['Cag'].";".
			$cnt_file['Filiale'].";".
			$cnt_file['NomeFiliale'].";".
			$cnt_file['Genere'].";".	
			$cnt_file['NumeroBuono'].";".
			$cnt_file['DataConsegnaBuono'].";".
			$cnt_file['IpAddress']."\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/rilasciobuoni.csv">Scarica il dettaglio completo dei buoni consegnati</a>';


?>