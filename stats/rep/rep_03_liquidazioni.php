<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

echo '
<center>
<div class="col-lg-12">
  <div class="alert alert-dismissible alert-success"><h3>Liquidazioni</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO PER TIPOLOGIA
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary">
        <strong>Liquidazioni DA FARE
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
                count(*)     as qta, 
                sum(Importo) as Importo
                FROM TAB_DECADUTI_NONLIQUIDATI
                WHERE filiale <> 999 
                ".$condizionefiliale."
                GROUP BY Tipo WITH ROLLUP
                 ";

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table border="0" valign="top" width="90%">
        <tr class="table-light" style="color:black;border-bottom:1px;">
          <td align="left"><b>Tipo</b></td>
          <td align="right"><b>Qtà</b></td>
          <td align="right"><b>Importo</b></td>
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

    if ($row_areeA['Tipo'] == "") {$titolo = '<b>TOTALE</b>'; $stile = " class='table-light' ";} else {$titolo = $row_areeA['Tipo']; $stile="  style='color:gray;'";}

    $tab_dettaglioA .=  "<tr>
                          <td align='left' width='70%'>".$titolo."&nbsp;</td>
                          <td align='right' width='10%'>".number_format($row_areeA['qta'],0,',','.')."&nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeA['Importo'],0,',','.')."</td>
                        </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';
      

?>


<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>


<?php



     // Se la richiesta arriva da una Filiale, NON mostro la tabella dei Totali di Filiale
     if (!isset($_GET['area']))   
     { echo ''; }
     else
     {
?>
<table border="0" align="center" width=50%">
  <tr>     
       <td valign="top"><?php echo $tab_dettaglioA1; ?></td>
  </tr>
</table>
<br>


<?php
    }
?>

