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
  <div class="alert alert-dismissible alert-success"><h3>Giovani Under 35</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
       

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER FASCE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary" align="center">
        <strong>Giovani con età tra 17 e 35 anni
       </div>';

$dettaglioA = " 
                SELECT
                    eta, 
                    sum(qta_socio_si + qta_socio_no) as qta,
                    sum(qta_socio_si) as qta_socio_si,
                    (sum(qta_socio_si) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_socio_si,
                    sum(qta_socio_no) as qta_socio_no,
                    (sum(qta_socio_no) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_socio_no,
                    sum(qta_rapporti_si) as qta_rapporti_si,
                    (sum(qta_rapporti_si) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_rapporti_si,
                    sum(qta_rapporti_no) as qta_rapporti_no,
                    (sum(qta_rapporti_no) / sum(qta_socio_si + qta_socio_no) * 100) as perc_qta_rapporti_no
                FROM view_under35
                WHERE eta between 18 and 35
                ".$condizionefiliale4."
                GROUP BY eta
                ORDER BY 1
                 ";
$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '<table border="0" valign="top" width="90%" align="center">
        <tr class="table-light" style="color:black;">
          <td align="left"  width="20%">&nbsp;Età</td>
          <td align="right" width="20%">Qtà Anagrafiche&nbsp;</td>
          <td align="right" width="20%">con Rapporti&nbsp;</td>
          <td align="right" width="20%">senza Rapporti&nbsp;</td>
          <td align="right" width="20%">di cui già Soci Banca&nbsp;</td>          
        </tr>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {
    
    $tab_dettaglioA .=  "<tr>
                          <td align='left' width='20%'><h6>&nbsp;".$row_areeA['eta']."&nbsp; &nbsp;</td>
                          <td align='right' width='20%'>
                          <b>".number_format($row_areeA['qta'],0,',','.')."</b>&nbsp;
                          </td>                          
                          <td align='right' width='20%'>
                          <b>".number_format($row_areeA['qta_rapporti_si'],0,',','.')."</b>&nbsp;
                          <i><small>".number_format($row_areeA['perc_qta_rapporti_si'],2,',','.')."%&nbsp;</td>  
                          <td align='right' width='20%'>
                          <b>".number_format($row_areeA['qta_rapporti_no'],0,',','.')."</b>&nbsp;
                          <i><small>".number_format($row_areeA['perc_qta_rapporti_no'],2,',','.')."%&nbsp;</td> 
                          </td>  
                          <td align='right' width='20%'>
                          <span style='color:green;'><b>".number_format($row_areeA['qta_socio_si'],0,',','.')."</b></span>&nbsp;
                          <i><small>".number_format($row_areeA['perc_qta_socio_si'],2,',','.')."%&nbsp;
                          </td> 
                         </tr>";
  // chiudo ciclo WHILE  
  }

$dettaglioA_TOT = " 
                SELECT
                    sum(qta_socio_si + qta_socio_no) as qta,
                    sum(qta_rapporti_si) as qta_rapporti_si,
                    sum(qta_rapporti_no) as qta_rapporti_no,
                    sum(qta_socio_si) as qta_socio_si
                FROM view_under35
                WHERE eta between 18 and 35
                ".$condizionefiliale4."
                 ";
$result_areeA_TOT = $dbhandle->query($dettaglioA_TOT) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
  while ($row_areeA_TOT = mysqli_fetch_array($result_areeA_TOT)) {
    $tab_dettaglioA .=  "<tr>
                          <td align='right' width='20%'><h6>&nbsp;TOTALE &nbsp;</td>
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta'],0,',','.')."</a>&nbsp;
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta_rapporti_si'],0,',','.')."</a>&nbsp;
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta_rapporti_no'],0,',','.')."</a>&nbsp;
                          <td align='right' width='20%'>".number_format($row_areeA_TOT['qta_socio_si'],0,',','.')."</a>&nbsp;
                          </td>                          
                         </tr>";
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';
?>


<table border="0" align="center" width="65%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>

<?php
    
// closing database connection      
$dbhandle->close();       
?>

