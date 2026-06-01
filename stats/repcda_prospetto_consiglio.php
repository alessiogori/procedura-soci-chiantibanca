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
        <title>Prospetto CDA</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 
      <body>
        ';

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");
$adesso_anno = date("Y");

$data1 = new DateTime('2020-01-01');
$data2 = new DateTime(date("Y-m-d"));
$mesi = $data2->diff($data1); 
$numeromesi = (($mesi->y) * 12) + ($mesi->m);
// echo $howeverManyMonths;

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);


// NON LIQUIDATI
$contenutofileA0 = '';
$myfileA0 = fopen("../tmp/cda_nonliquidati.csv", "w");
$contenutofileA0 .= "Nag;IDSocio;Nominativo;DataEntrata;DataDecadenza;Filiale;NrAzioni;Importo;Tipo;Sofferenza\n";

$dettaglioA0= " 
                SELECT
                NAG, IDSOCIO, NOMINATIVO, DATA_ENTRATA,  
                DATA_DECADENZA, FILIALE, NUMERO_AZIONI, IMPORTO, TIPOLOGIA_USCITA, SOFFERENZA
                from TAB_DECADUTI_NONLIQUIDATI 
                where FILIALE <> 999  
                ORDER BY NOMINATIVO
                ";
$result_areeA0 = $dbhandle->query($dettaglioA0) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while ($row_areeA0 = mysqli_fetch_array($result_areeA0)) {
    $contenutofileA0 .=  
          $row_areeA0['NAG'].";".
          $row_areeA0['IDSOCIO'].";".
          $row_areeA0['NOMINATIVO'].";".
          $row_areeA0['DATA_ENTRATA'].";".
          $row_areeA0['DATA_DECADENZA'].";".
          $row_areeA0['FILIALE'].";".
          number_format($row_areeA0['NUMERO_AZIONI'],2,',','.').";".
          number_format($row_areeA0['IMPORTO'],0,',','.').";".
          $row_areeA0['TIPOLOGIA_USCITA'].";".
          $row_areeA0['SOFFERENZA']."\n";
  }
fwrite($myfileA0, $contenutofileA0);
fclose($myfileA0);

/// LIQUIDATI
$contenutofileA1 = '';
$myfileA1 = fopen("../tmp/cda_liquidati.csv", "w");
$contenutofileA1 .= "Nag;IDSocio;Nominativo;DataEntrata;DataMovimentoDecadenza;DataDecadenza;DataMovimentoLiquidazione;DataDelibera;DataPagamento;Filiale;Importo;Sovrapprezzo;Tipo;Sofferenza\n";

$dettaglioA1= " 
                SELECT
                NAG, IDSOCIO, NOMINATIVO, DATA_ENTRATA, DATA_MOVIMENTO_DECADENZA,
                DATA_DECADENZA, DATA_MOVIMENTO_LIQUIDAZIONE, DATA_DELIBERA, DATA_PAGAMENTO,
                FILIALE, IMPORTO, SOVRAPPREZZO, TIPOLOGIA_USCITA, SOFFERENZA
                from TAB_DECADUTI_LIQUIDATI 
                where FILIALE <> 999  
                ORDER BY NOMINATIVO
                ";
$result_areeA1 = $dbhandle->query($dettaglioA1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
  while ($row_areeA1 = mysqli_fetch_array($result_areeA1)) {
    $contenutofileA1 .=  
          $row_areeA1['NAG'].";".
          $row_areeA1['IDSOCIO'].";".
          $row_areeA1['NOMINATIVO'].";".
          $row_areeA1['DATA_ENTRATA'].";".
          $row_areeA1['DATA_MOVIMENTO_DECADENZA'].";".
          $row_areeA1['DATA_DECADENZA'].";".
          $row_areeA1['DATA_MOVIMENTO_LIQUIDAZIONE'].";".
          $row_areeA1['DATA_DELIBERA'].";".
          $row_areeA1['DATA_PAGAMENTO'].";".
          $row_areeA1['FILIALE'].";".
          number_format($row_areeA1['IMPORTO'],2,',','.').";".
          number_format($row_areeA1['SOVRAPPREZZO'],0,',','.').";".
          $row_areeA1['TIPOLOGIA_USCITA'].";".
          $row_areeA1['SOFFERENZA']."\n";
  }
fwrite($myfileA1, $contenutofileA1);
fclose($myfileA1);

?>

<div class="alert alert-dismissible alert-warning">
  <h2 class="alert-heading">Prospetto CDA</h2>
  <p class="mb-0 justify-content-between align-items-left">Situazione andamentale per Consiglio di Amministrazione (fare copia-incolla dei grafici o tabelle).</p>
</div>

<table width="90%" align="center">
    <tr>
        <td>
            <a style="font-size: 30px;" href="situazione.php?f=999" target="_blank">01 - Situazione</a>
            <br>
            Copiare prima tabella, grafico andamentale, tabelle medie annuali e per Area
            <br><br>
            <a style="font-size: 30px;" href="fasce_consenzarichieste.php" target="_blank">02 - Fasce quote</a>
            <br>
            Copiare prima tabella
            <br><br>
            <a style="font-size: 30px;" href="liquidazioni_grafico.php" target="_blank">03 - Liquidazioni</a>
            <br>
            Copiare intere tabelle 
            <br><br>
            <a style="font-size: 30px;" href="cessioni_grafico.php" target="_blank">04 - Cessioni a Banca</a>
            <br>
            Copiare grafico bianco + tabella di sinistra (Aree, fasce di importo, storico)
            <br><br>
            <a style="font-size: 30px;" href="esclusioni_grafico.php" target="_blank">05 - Esclusioni</a>
            <br>
            Copiare grafico bianco 
            <br><br>
            <a style="font-size: 30px;" href="..\deceduti.php" target="_blank">06 - Deceduti</a>
            <br>
            Prendere dati da prima tabella 
            <br><br>
            <a style="font-size: 30px;" href="../tmp/cda_liquidati.csv" target="_blank">Per Excel "LIQUIDAZIONI" - Posizioni Liquidate</a>
            <br>
            Da riportare su foglio "DettaglioLiquidate"
            <br><br>
            <a style="font-size: 30px;" href="../tmp/cda_nonliquidati.csv" target="_blank">Per Excel "LIQUIDAZIONI" - Posizioni NON Liquidate</a>
            <br>
            Da riportare su foglio "DettaglioDaLiquidare"
        </td>
    </tr>
</table>

<center>
    <br><br>
    <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
</center>

</body>
</html>