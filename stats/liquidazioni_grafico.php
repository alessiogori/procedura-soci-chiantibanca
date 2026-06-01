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
        <title>Stats Liquidazioni</title>
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
if (!isset($_GET['key']))
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

echo '
  <div class="alert alert-dismissible alert-warning">
      <h2 class="alert-heading">Liquidazioni '.$titolofiliale.'</h2>
      <p class="mb-0 justify-content-between align-items-left">Questo report rappresenta la situazione delle liquidazioni ancora da fare.<br>'.$info.'
        Inventario aggiornato al '.$datafile.'</p>
  </div>
';
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DECADENZA AVVENUTA PRIMA DELL'ANNO PRECEDENTE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA0 =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Decadenza precedente al '.$iniziodecadenza.' <br>da liquidare entro il '.$adesso_anno.'
       </div>';

// Interrogo la view

$dettaglioA0= " 
                SELECT
                    case 
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza = 'S') then 'ESCLUSIONE SOFFERENZA'
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza != 'S') then 'ESCLUSIONE'
                    when tipologia_uscita = 'MORTE' then 'MORTE'
                    when tipologia_uscita = 'RECESSO' then 'RECESSO'
                    else ''
                    end as Tipo,
                    count(*) as qta,
                    sum(importo) as Importo
                from TAB_DECADUTI_NONLIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                    and importo <> 0 
                    and DATA_DECADENZA <= '$iniziodecadenza"."-01-01"."'
                group by Tipo with ROLLUP 
                ";

$result_areeA0 = $dbhandle->query($dettaglioA0) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA0 .= '<table class="table" border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left">Tipo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA0 = mysqli_fetch_array($result_areeA0)) {

    if ($row_areeA0['Tipo'] == '') 
        {$row_areeA0['Tipo'] = 'TOTALE'; 
         $sfondo = "<tr class='table-secondary'>";
     }
     else 
     {$sfondo = "<tr>";}

    $tab_dettaglioA0 .=  $sfondo."
                          <td align='left' width='70%'>".$row_areeA0['Tipo']."&nbsp;</td>
                          <td align='right' width='10%'>".number_format($row_areeA0['qta'],0,',','.')."&nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeA0['Importo'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA0 .=  '</table>';


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DECADENZA AVVENUTA NELL'ANNO PRECEDENTE 
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Decadenza del '.$iniziodecadenza.' <br>da liquidare entro il '.$adesso_anno.'
       </div>';

// Interrogo la view

$dettaglioA = " 
                SELECT
                    case 
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza = 'S') then 'ESCLUSIONE SOFFERENZA'
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza != 'S') then 'ESCLUSIONE'
                    when tipologia_uscita = 'MORTE' then 'MORTE'
                    when tipologia_uscita = 'RECESSO' then 'RECESSO'
                    else ''
                    end as Tipo,
                    count(*) as qta,
                    sum(importo) as Importo
                from TAB_DECADUTI_NONLIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                    and importo <> 0 
                    and DATA_DECADENZA between '$iniziodecadenza"."-01-01"."'
                                        and '$iniziodecadenza"."-12-31"."'
                group by Tipo with ROLLUP 
                ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table class="table" border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left">Tipo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

    if ($row_areeA['Tipo'] == '') 
        {$row_areeA['Tipo'] = 'TOTALE'; 
         $sfondo = "<tr class='table-secondary'>";
     }
     else 
     {$sfondo = "<tr>";}

    $tab_dettaglioA .=  $sfondo."
                          <td align='left' width='70%'>".$row_areeA['Tipo']."&nbsp;</td>
                          <td align='right' width='10%'>".number_format($row_areeA['qta'],0,',','.')."&nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeA['Importo'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DECADENZA OLTRE L'ANNO IN CORSO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioB =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Decadenza del '.$adesso_anno.' <br>da liquidare nel '.$proxdecadenza.'
       </div>';

// Interrogo la view

$dettaglioB = " 
                SELECT
                    case 
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza = 'S') then 'ESCLUSIONE SOFFERENZA'
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza != 'S') then 'ESCLUSIONE'
                    when tipologia_uscita = 'MORTE' then 'MORTE'
                    when tipologia_uscita = 'RECESSO' then 'RECESSO'
                    else ''
                    end as Tipo,
                    count(*) as qta,
                    sum(importo) as Importo
                from TAB_DECADUTI_NONLIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                    and importo <> 0 
                    and DATA_DECADENZA >= '$adesso_anno"."-01-01"."'
                group by Tipo with ROLLUP 
                ";

$result_areeB = $dbhandle->query($dettaglioB) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioB .= '<table class="table" border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left">Tipo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeB = mysqli_fetch_array($result_areeB)) {

    if ($row_areeB['Tipo'] == '') 
        {$row_areeB['Tipo'] = 'TOTALE'; 
         $sfondo = "<tr class='table-secondary'>";
     }
     else 
     {$sfondo = "<tr>";}

    $tab_dettaglioB .=  $sfondo."
                          <td align='left' width='70%'>".$row_areeB['Tipo']."&nbsp;</td>
                          <td align='right' width='10%'>".number_format($row_areeB['qta'],0,',','.')."&nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeB['Importo'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }


// Chiudo la tabella
$tab_dettaglioB .=  '</table>';



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// TOTALI GENERALI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioC =  ' <div class="alert alert-dismissible alert-danger">
        <strong>Liquidazioni Totali DA EFFETTUARE
       </div>';

// Interrogo la view

$dettaglioC = " 
                SELECT
                    case 
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza = 'S') then 'ESCLUSIONE SOFFERENZA'
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza != 'S') then 'ESCLUSIONE'
                    when tipologia_uscita = 'MORTE' then 'MORTE'
                    when tipologia_uscita = 'RECESSO' then 'RECESSO'
                    else ''
                    end as Tipo,
                    count(*) as qta,
                    sum(importo) as Importo
                from TAB_DECADUTI_NONLIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                    and importo <> 0 
                group by Tipo with ROLLUP 
                ";

$result_areeC = $dbhandle->query($dettaglioC) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioC .= '<table class="table" border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left">Tipo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeC = mysqli_fetch_array($result_areeC)) {

    if ($row_areeC['Tipo'] == '') 
        {$row_areeC['Tipo'] = 'TOTALE'; 
         $sfondo = "<tr class='table-secondary'>";
     }
     else 
     {$sfondo = "<tr>";}

    $tab_dettaglioC .=  $sfondo."
                          <td align='left' width='70%'>".$row_areeC['Tipo']."&nbsp;</td>
                          <td align='right' width='10%'>
                            <a href='liquidazioni_dettaglio.php?fonte=nonpagate&key=".$filiale."&tipo=".$row_areeC['Tipo']."'>".number_format($row_areeC['qta'],0,',','.')."</a>&nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeC['Importo'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }


// Chiudo la tabella
$tab_dettaglioC .=  '</table>';



// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// LIQUIDAZIONI EFFETTUATE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglio_LiqEff =  ' <div class="alert alert-dismissible alert-success">
        <strong>Liquidazioni EFFETTUATE
       </div>';

// Interrogo la view

$dettaglio_LiqEff = " 
                SELECT
                DATE_FORMAT(DATA_MOVIMENTO_LIQUIDAZIONE ,'%Y') as AnnoLiquidazione,
                TIPOLOGIA_USCITA as Tipo, 
                SOFFERENZA as Sofferenza,
                count(*) as qta,
                round(sum(IMPORTO),2) as Importo
                FROM TAB_DECADUTI_LIQUIDATI
                WHERE FILIALE <> 999  
                AND CTIPOMOV not in ('RS','SU')
                ".$condizionefiliale."
                /* PER CLAUDIO PACI */
                -- AND DATA_MOVIMENTO_LIQUIDAZIONE <= '2023-09-30'
                /* ------------- */            
                GROUP BY AnnoLiquidazione, Tipo with rollup 
                -- ORDER BY AnnoLiquidazione, Tipo
                ";

// echo $dettaglio_LiqEff;
$result_aree_LiqEff = $dbhandle->query($dettaglio_LiqEff) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglio_LiqEff .= '<table class="table" border="1" valign="top" width="100%">
        <tr class="table-secondary">
          <td align="left">Anno Liquidazione</td>
          <td align="left">Tipo</td>
          <td align="right">Qtà</td>
          <td align="right">Importo</td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_aree_LiqEff = mysqli_fetch_array($result_aree_LiqEff)) {

    if ($row_aree_LiqEff['Tipo'] == '') 
        {$row_aree_LiqEff['Tipo'] = 'TOTALE'; 
         $sfondo = "<tr class='table-secondary'>";
     }
     else 
     {$sfondo = "<tr>";}

    $tab_dettaglio_LiqEff .=  $sfondo."
                          <td align='left' width='70%'>".$row_aree_LiqEff['AnnoLiquidazione']."&nbsp;</td>
                          <td align='left' width='70%'>".$row_aree_LiqEff['Tipo']."&nbsp;</td>
                          <td align='right' width='10%'>
                            <a href='liquidazioni_dettaglio.php?fonte=pagate&key=".$filiale."&annoliq=".$row_aree_LiqEff['AnnoLiquidazione']."&tipo=".$row_aree_LiqEff['Tipo']."&soff=".$row_aree_LiqEff['Sofferenza']."'>
                          ".number_format($row_aree_LiqEff['qta'],0,',','.')."</a>&nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_aree_LiqEff['Importo'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }


// Chiudo la tabella
$tab_dettaglio_LiqEff .=  '</table>';



?>

<table border="0" align="center" width="60%">
  <tr>     
       <td valign="top" width="33%"><?php echo $tab_dettaglioA0; ?></td>
       <td>&nbsp;&nbsp;&nbsp;</td>
       <td valign="top" width="33%"><?php echo $tab_dettaglioA; ?></td>
       <td>&nbsp;&nbsp;&nbsp;</td>
       <td valign="top" width="33%"><?php echo $tab_dettaglioB; ?></td>
  </tr>
</table>
<br>

<table border="0" align="center" width="60%">
  <tr>     
       <td valign="top" width="33%"><?php echo $tab_dettaglioC; ?></td>
       <td>&nbsp;&nbsp;&nbsp;</td>
       <td valign="top" width="33%"><?php echo $tab_dettaglio_LiqEff; ?></td>
       <td>&nbsp;&nbsp;&nbsp;</td>
       <td valign="top" width="33%"><?php echo ''; ?></td>
  </tr>
</table>
<br>

<br>

<?php


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// ESPORTAZIONE ELENCO GENERALE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
						
    // Preparo il file per l'estrazione in CSV
    $contenutofile = '';
    $select_file = "	

                    SELECT FILIALE, NAG, IDSOCIO, NOMINATIVO, DATA_DECADENZA, IMPORTO, 
                    case 
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza = 'S') then 'ESCLUSIONE SOFFERENZA'
                    when (tipologia_uscita = 'ESCLUSIONE' and Sofferenza != 'S') then 'ESCLUSIONE'
                    when tipologia_uscita = 'MORTE' then 'MORTE'
                    when tipologia_uscita = 'RECESSO' then 'RECESSO'
                    else ''
                    end as Tipo
                from TAB_DECADUTI_NONLIQUIDATI 
                where filiale <> 999  
                ".$condizionefiliale."
                    and importo <> 0 ";

    //echo $select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");    
    $myfile = fopen("../tmp/liquidazioni".$filiale.".csv", "w");
    $contenutofile .= "filiale;Nag;IDSocio;Nominativo;DataDecadenza;Importo;Tipo\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
			$cnt_file['FILIALE'].";".
			$cnt_file['NAG'].";".
            $cnt_file['IDSOCIO'].";".
			$cnt_file['NOMINATIVO'].";".
			$cnt_file['DATA_DECADENZA'].";".
			$cnt_file['IMPORTO'].";".		
			$cnt_file['Tipo']."\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<center><small style="color:gray;"><i>Dati tratti dai movimenti registrati in Sicra su procedura Soci</i></small></center>
          <br>';

    echo '<center><a class="btn btn-outline-warning" href="../tmp/liquidazioni'.$filiale.'.csv">Scarica il dettaglio completo</a><br><br>';


     // Se la richiesta arriva da una Filiale, NON mostro la tabella dei Totali di Filiale



?>

<br>

<?php
// closing database connection      
$dbhandle->close();       
?>

    <center>
        <br><br>
        <a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png">
    </center>

</body>
</html>