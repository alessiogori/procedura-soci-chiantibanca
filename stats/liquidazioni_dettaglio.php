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
        <title>Stats Liquidazioni - Dettaglio</title>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
            .blink_me {
                      animation: blinker 2s linear infinite;
             }

            @keyframes blinker {
              50% {
                opacity: 0;
              }
            }
        </style> 

        <body><br><br>

        <script>
            function selectElementContents(el) {
                var body = document.body, range, sel;
                if (document.createRange && window.getSelection) {
                    range = document.createRange();
                    sel = window.getSelection();
                    sel.removeAllRanges();
                    try {
                        range.selectNodeContents(el);
                        sel.addRange(range);
                    } catch (e) {
                        range.selectNode(el);
                        sel.addRange(range);
                    }
                } else if (body.createTextRange) {
                    range = body.createTextRange();
                    range.moveToElementText(el);
                    range.select();
                }
            }
        </script>

        ';

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

$adesso = date("d.m.Y");
$adesso_anno = date("Y");
$iniziodecadenza = $adesso_anno - 1;
$proxdecadenza = $adesso_anno + 1;
//$adesso_anno = 2023;

$data1 = new DateTime('2019-01-01');
$data2 = new DateTime(date("Y-m-d"));
$mesi = $data2->diff($data1); 
$numeromesi = (($mesi->y) * 12) + ($mesi->m);
// echo $howeverManyMonths;

// Controllo se è stato richiesto un periodo particolare
if (!isset($_GET['periodo']))
    {
   $datarichiesta = $adesso_anno - 1;    // conteggio da un anno indietro rispetto ad oggi
    }
    else
    {
   $datarichiesta = $_GET['periodo'];
  }

// Controllo se la richiesta arriva   
if (empty($_GET['key']))
    {$condizionefiliale = '';
     $titolofiliale = '';
     $filiale = '';
     $area = '';
     $info = ''; 
     //$info = '<br><div class="blink_me">Aggiornare i dati da Sicra > Soci > Riepiloghi > Stampa Soci Decaduti (Liquidati / Non Liquidati)</div>';
    }
    else
    {
  // da un FILIALE
     if (!isset($_GET['area']))   
     {    
     $condizionefiliale = "AND filiale = ".substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
     $info = '';
     }
     else
  // da una AREA   
     {    
     $condizionefiliale = 'AND filiale in ('.$_GET['key'].')';
     $titolofiliale = ' - Area '.$_GET['area'];  
     $filiale = $_GET['area'];
     $info = '';
     }
    }


$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// Verifica data file di inventario
$select_datafile = "SELECT * FROM tab_ultimo_caricamento WHERE fonte = 'TAB_DECADUTI_NONLIQUIDATI' " ;
$qry_datafile = $dbhandle->query($select_datafile) 
                or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_datafile = mysqli_fetch_array($qry_datafile)){ 
  $datafile = $dati_datafile['caricamento'];
}



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO LIQUIDAZIONI - EFFETTUATE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
if ($_GET['fonte'] == 'pagate')
{

    echo '
      <div class="alert alert-dismissible alert-warning">
          <h2 class="alert-heading">Liquidazioni Effettuate '.$titolofiliale.' (dettaglio)</h2>
          <p class="mb-0 justify-content-between align-items-left">'.$info.'
            Inventario aggiornato al '.$datafile.'</p>
      </div>
    ';


$dettaglioA0= " 
                SELECT
                NAG, IDSOCIO, NOMINATIVO, DATA_ENTRATA, DATA_MOVIMENTO_DECADENZA, 
                DATA_DECADENZA, DATA_MOVIMENTO_LIQUIDAZIONE, DATA_DELIBERA,
                DATA_PAGAMENTO, FILIALE, IMPORTO, SOVRAPPREZZO, TIPOLOGIA_USCITA, CTIPOMOV, SOFFERENZA
                from TAB_DECADUTI_LIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                and DATE_FORMAT(DATA_MOVIMENTO_LIQUIDAZIONE,'%Y') = ".$_GET['annoliq']."
                and TIPOLOGIA_USCITA = '".$_GET['tipo']."'
                ORDER BY NOMINATIVO
                ";

// echo $dettaglioA0;

$result_areeA0 = $dbhandle->query($dettaglioA0) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA0 = '<table id="dataTable" class="table" border="1" valign="top" align="center" width="100%">
        <tr class="table-secondary">
          <td align="left">NAG</td>
          <td align="left">IDSOCIO</td>
          <td align="left">Nominativo</td>
          <td align="left">Data<br>Entrata</td>
          <td align="left">Data<br>Movimento<br>Decadenza</td>
          <td align="left">Data<br>Decadenza</td>
          <td align="left">Data<br>Movimento<br>Liquidazione</td>
          <td align="left">Data<br>Delibera</td>
          <td align="left">Data<br>Pagamento</td>
          <td align="left">Filiale</td>
          <td align="left">Importo</td>
          <td align="left">Sovrapprezzo</td>
          <td align="left">Tipologia Uscita</td>
          <td align="left">Motivo Uscita</td>
          <td align="left">Sofferenza</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA0 = mysqli_fetch_array($result_areeA0)) {

    if ($row_areeA0['CTIPOMOV'] == 'RL') {$tipouscita = 'Rimborso Eredi'; $color='';}
    elseif ($row_areeA0['CTIPOMOV'] == 'RS') {$tipouscita = 'Subentro Erede'; $color='orange';}
    elseif ($row_areeA0['CTIPOMOV'] == 'SU') {$tipouscita = 'Subentro Erede'; $color='orange';}
    else {$tipouscita = ''; $color='';}

    if ($row_areeA0['SOFFERENZA'] == 'S') {$color1='red';}
    else {$color1='';}

    $tab_dettaglioA0 .=  "
        <tr style='color:".$color1.$color.";'>
          <td align='left'>".$row_areeA0['NAG']."</td>
          <td align='left'>".$row_areeA0['IDSOCIO']."</td>
          <td align='left'>".$row_areeA0['NOMINATIVO']."</td>
          <td align='left'>".$row_areeA0['DATA_ENTRATA']."</td>
          <td align='left'>".$row_areeA0['DATA_MOVIMENTO_DECADENZA']."</td>
          <td align='left'>".$row_areeA0['DATA_DECADENZA']."</td>
          <td align='left'>".$row_areeA0['DATA_MOVIMENTO_LIQUIDAZIONE']."</td>
          <td align='left'>".$row_areeA0['DATA_DELIBERA']."</td>
          <td align='left'>".$row_areeA0['DATA_PAGAMENTO']."</td>
          <td align='left'>".$row_areeA0['FILIALE']."</td>
          <td align='right'>".number_format($row_areeA0['IMPORTO'],2,',','.')."</td>
          <td align='right'>".number_format($row_areeA0['SOVRAPPREZZO'],0,',','.')."</td>
          <td align='left'>".$row_areeA0['TIPOLOGIA_USCITA']."</td>
          <td align='left'>".$tipouscita."</td>
          <td align='left'>".$row_areeA0['SOFFERENZA']."</td>
        </tr>";

  }

// Chiudo la tabella
$tab_dettaglioA0 .=  '</table>';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> &nbsp;&nbsp; <a href="#u30" style="text-decoration:none;">&dArr;</a></center><br>';

    echo '<center><small style="color:gray;"><i>Dati tratti dai movimenti registrati in Sicra su procedura Soci</i></small></center>
          <br>';
?>


<table id="dataTable" border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="24%"><?php echo $tab_dettaglioA0; ?></td>
  </tr>
</table>
<br>

<?php
}

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO LIQUIDAZIONI - DA EFFETTUARE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
if ($_GET['fonte'] == 'nonpagate')
{
    echo '
      <div class="alert alert-dismissible alert-warning">
          <h2 class="alert-heading">Liquidazioni da Effettuare '.$titolofiliale.' (dettaglio)</h2>
          <p class="mb-0 justify-content-between align-items-left">'.$info.'
            Inventario aggiornato al '.$datafile.'</p>
      </div>
    ';

$dettaglioA0= " 
                SELECT
                NAG, IDSOCIO, NOMINATIVO, DATA_ENTRATA,
                DATA_DECADENZA, FILIALE, NUMERO_AZIONI,
                IMPORTO, TIPOLOGIA_USCITA, SOFFERENZA
                from TAB_DECADUTI_NONLIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                and TIPOLOGIA_USCITA = '".$_GET['tipo']."'
                ORDER BY NOMINATIVO
                ";

// echo $dettaglioA0;

$result_areeA0 = $dbhandle->query($dettaglioA0) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA0 = '<table id="dataTable" class="table" border="1" valign="top" align="center" width="100%">
        <tr class="table-secondary">
          <td align="left">NAG</td>
          <td align="left">IDSOCIO</td>
          <td align="left">Nominativo</td>
          <td align="left">Data Entrata</td>
          <td align="left">Data Decadenza</td>
          <td align="left">Filiale</td>
          <td align="left">Nr.Azioni</td>
          <td align="left">Importo</td>
          <td align="left">Tipologia Uscita</td>
          <td align="left">Sofferenza</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA0 = mysqli_fetch_array($result_areeA0)) {

    $tab_dettaglioA0 .=  "
          <td align='left'>".$row_areeA0['NAG']."</td>
          <td align='left'>".$row_areeA0['IDSOCIO']."</td>
          <td align='left'>".$row_areeA0['NOMINATIVO']."</td>
          <td align='left'>".$row_areeA0['DATA_ENTRATA']."</td>
          <td align='left'>".$row_areeA0['DATA_DECADENZA']."</td>
          <td align='left'>".$row_areeA0['FILIALE']."</td>
          <td align='right'>".number_format($row_areeA0['NUMERO_AZIONI'],0,',','.')."</td>
          <td align='right'>".number_format($row_areeA0['IMPORTO'],2,',','.')."</td>
          <td align='left'>".$row_areeA0['TIPOLOGIA_USCITA']."</td>
          <td align='left'>".$row_areeA0['SOFFERENZA']."</td>
        </tr>";

  }

// Chiudo la tabella
$tab_dettaglioA0 .=  '</table>';

echo '<a name="lista"><center><input type="button" class="btn btn-outline-warning"  value="Seleziona tabella per CTRL+C" onclick="selectElementContents( document.getElementById(\'dataTable\') );"> &nbsp;&nbsp; <a href="#u30" style="text-decoration:none;">&dArr;</a></center><br>';

    echo '<center><small style="color:gray;"><i>Dati tratti dai movimenti registrati in Sicra su procedura Soci</i></small></center>
          <br>';
?>


<table id="dataTable" border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="24%"><?php echo $tab_dettaglioA0; ?></td>
  </tr>
</table>
<br>


<?php
}
// closing database connection      
$dbhandle->close();       
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>